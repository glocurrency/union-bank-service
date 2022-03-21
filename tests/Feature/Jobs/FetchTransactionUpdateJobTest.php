<?php

namespace GloCurrency\UnionBank\Tests\Feature\Jobs;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use GloCurrency\UnionBank\Tests\FeatureTestCase;
use GloCurrency\UnionBank\Models\Transaction;
use GloCurrency\UnionBank\Jobs\FetchTransactionUpdateJob;
use GloCurrency\UnionBank\Events\TransactionCreatedEvent;
use GloCurrency\UnionBank\Enums\TransactionStateCodeEnum;
use BrokeYourBike\UnionBank\Enums\PaymentStatusEnum;
use BrokeYourBike\UnionBank\Enums\ErrorCodeEnum;
use BrokeYourBike\UnionBank\Client;

class FetchTransactionUpdateJobTest extends FeatureTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Event::fake([
            TransactionCreatedEvent::class,
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

    /** @test */
    public function it_can_update_state_code()
    {
        $targetTransaction = Transaction::factory()->create([
            'state_code' => TransactionStateCodeEnum::PROCESSING,
            'remote_reference' => 'ref-1',
        ]);

        [$httpMock] = $this->mockApiFor(Client::class);
        $httpMock->append($this->makeAuthResponse());
        $httpMock->append(new \GuzzleHttp\Psr7\Response(200, [], '{
            "responseCode": "'. ErrorCodeEnum::SUCCESS->value .'",
            "responseMessage": "Status successfully gotten",
            "transactionReference": "ref-1",
            "transactionStatus": "'. PaymentStatusEnum::SUCCESSFUL->value .'"
        }'));

        FetchTransactionUpdateJob::dispatchSync($targetTransaction);

        $targetTransaction = $targetTransaction->fresh();
        $this->assertInstanceOf(Transaction::class, $targetTransaction);

        $this->assertEquals(TransactionStateCodeEnum::PAID, $targetTransaction->state_code);
    }
}
