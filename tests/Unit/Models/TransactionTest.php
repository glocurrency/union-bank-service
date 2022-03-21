<?php

namespace GloCurrency\UnionBank\Tests\Unit\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use GloCurrency\UnionBank\Tests\TestCase;
use GloCurrency\UnionBank\Models\Transaction;
use GloCurrency\UnionBank\Enums\TransactionStateCodeEnum;
use BrokeYourBike\UnionBank\Enums\ErrorCodeEnum;
use BrokeYourBike\HasSourceModel\SourceModelInterface;
use BrokeYourBike\BaseModels\BaseUuid;

class TransactionTest extends TestCase
{
    /** @test */
    public function it_extends_base_model(): void
    {
        $parent = get_parent_class(Transaction::class);

        $this->assertSame(BaseUuid::class, $parent);
    }

    /** @test */
    public function it_uses_soft_deletes(): void
    {
        $usedTraits = class_uses(Transaction::class);

        $this->assertArrayHasKey(SoftDeletes::class, $usedTraits);
    }

    /** @test */
    public function it_implemets_source_model_interface(): void
    {
        $this->assertInstanceOf(SourceModelInterface::class, new Transaction());
    }

    /** @test */
    public function it_returns_receive_amount_as_float(): void
    {
        $transaction = new Transaction();
        $transaction->receive_amount = '1.02';

        $this->assertSame(1.02, $transaction->receive_amount);
    }

    /** @test */
    public function it_returns_state_code_as_enum(): void
    {
        $transaction = new Transaction();
        $transaction->setRawAttributes([
            'state_code' => TransactionStateCodeEnum::LOCAL_UNPROCESSED->value,
        ]);

        $this->assertEquals(TransactionStateCodeEnum::LOCAL_UNPROCESSED, $transaction->state_code);
    }

    /** @test */
    public function it_returns_error_code_as_enum(): void
    {
        $transaction = new Transaction();
        $transaction->setRawAttributes([
            'error_code' => ErrorCodeEnum::SUCCESS->value,
        ]);

        $this->assertEquals(ErrorCodeEnum::SUCCESS, $transaction->error_code);
    }
}
