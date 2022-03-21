<?php

namespace GloCurrency\UnionBank\Tests\Feature\Jobs;

use Illuminate\Support\Facades\Event;
use GloCurrency\UnionBank\Tests\FeatureTestCase;
use GloCurrency\UnionBank\Models\Transaction;
use GloCurrency\UnionBank\Jobs\SendTransactionJob;
use GloCurrency\UnionBank\Exceptions\SendTransactionException;
use GloCurrency\UnionBank\Events\TransactionUpdatedEvent;
use GloCurrency\UnionBank\Events\TransactionCreatedEvent;
use GloCurrency\UnionBank\Enums\TransactionStateCodeEnum;
use BrokeYourBike\UnionBank\Enums\ErrorCodeEnum;
use BrokeYourBike\UnionBank\Client;

class SendTransactionJobTest extends FeatureTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Event::fake([
            TransactionCreatedEvent::class,
            TransactionUpdatedEvent::class,
        ]);
    }

    private function makeAuthResponse(): \GuzzleHttp\Psr7\Response
    {
        return new \GuzzleHttp\Psr7\Response(200, [], '{
            "access_token": "super-secure-token",
            "scope": "read write",
            "token_type": "Bearer",
            "expires_in": 600000
        }');
    }

    private function makeTransactionResponse(ErrorCodeEnum $responseCode, string $reference = ''): \GuzzleHttp\Psr7\Response
    {
        return new \GuzzleHttp\Psr7\Response(200, [], '{
            "responseCode": "'. $responseCode->value .'",
            "responseMessage": "",
            "transactionReference": "'. $reference .'",
            "errors": null
        }');
    }

    /**
     * @test
     * @dataProvider transactionStatesProvider
     */
    public function it_will_throw_if_state_not_LOCAL_UNPROCESSED(TransactionStateCodeEnum $stateCode, bool $shouldFail): void
    {
        /** @var Transaction */
        $targetTransaction = Transaction::factory()->create([
            'state_code' => $stateCode,
            'receive_amount' => 100.0,
        ]);

        [$httpMock] = $this->mockApiFor(Client::class);
        $httpMock->append($this->makeAuthResponse());
        $httpMock->append($this->makeTransactionResponse(ErrorCodeEnum::SUCCESS));

        if ($shouldFail) {
            $this->expectExceptionMessage("Transaction state_code `{$targetTransaction->state_code->value}` not allowed");
            $this->expectException(SendTransactionException::class);
        }

        SendTransactionJob::dispatchSync($targetTransaction);

        if (!$shouldFail) {
            $this->assertEquals(TransactionStateCodeEnum::PROCESSING, $targetTransaction->fresh()->state_code);
        }
    }

    public function transactionStatesProvider(): array
    {
        $states = collect(TransactionStateCodeEnum::cases())
            ->filter(fn($c) => !in_array($c, [
                TransactionStateCodeEnum::LOCAL_UNPROCESSED,
            ]))
            ->flatten()
            ->map(fn($c) => [$c, true])
            ->toArray();

        $states[] = [TransactionStateCodeEnum::LOCAL_UNPROCESSED, false];

        return $states;
    }

    /** @test */
    public function it_will_throw_if_error_code_is_unexpected(): void
    {
        /** @var Transaction */
        $targetTransaction = Transaction::factory()->create([
            'state_code' => TransactionStateCodeEnum::LOCAL_UNPROCESSED,
            'receive_amount' => 100.0,
        ]);

        [$httpMock] = $this->mockApiFor(Client::class);
        $httpMock->append($this->makeAuthResponse());
        $httpMock->append(new \GuzzleHttp\Psr7\Response(200, [], '{
            "responseCode": "not a code you can expect",
            "responseMessage": "KEK"
        }'));

        try {
            SendTransactionJob::dispatchSync($targetTransaction);
        } catch (\Throwable $th) {
            $this->assertEquals('Unexpected ' . ErrorCodeEnum::class . ': `not a code you can expect`', $th->getMessage());
            $this->assertInstanceOf(SendTransactionException::class, $th);
        }

        /** @var Transaction */
        $targetTransaction = $targetTransaction->fresh();

        $this->assertEquals(TransactionStateCodeEnum::UNEXPECTED_ERROR_CODE, $targetTransaction->state_code);
    }

    /** @test */
    public function it_can_send_transaction(): void
    {
        /** @var Transaction */
        $targetTransaction = Transaction::factory()->create([
            'state_code' => TransactionStateCodeEnum::LOCAL_UNPROCESSED,
            'receive_amount' => $this->faker->randomFloat(2, 1),
        ]);

        [$httpMock] = $this->mockApiFor(Client::class);
        $httpMock->append($this->makeAuthResponse());
        $httpMock->append($this->makeTransactionResponse(ErrorCodeEnum::SUCCESS, 'ref-123'));

        SendTransactionJob::dispatchSync($targetTransaction);

        /** @var Transaction */
        $targetTransaction = $targetTransaction->fresh();


        $this->assertEquals(TransactionStateCodeEnum::PROCESSING, $targetTransaction->state_code);
        $this->assertEquals(ErrorCodeEnum::SUCCESS, $targetTransaction->error_code);
        $this->assertSame('ref-123', $targetTransaction->remote_reference);
        // TODO: make more assetions
    }
}
