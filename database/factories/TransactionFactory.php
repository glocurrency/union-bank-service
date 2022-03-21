<?php

namespace GloCurrency\UnionBank\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use GloCurrency\UnionBank\UnionBank;
use GloCurrency\UnionBank\Models\Transaction;
use GloCurrency\UnionBank\Models\Sender;
use GloCurrency\UnionBank\Models\Recipient;
use GloCurrency\UnionBank\Models\Bank;
use GloCurrency\UnionBank\Enums\TransactionStateCodeEnum;

class TransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => $this->faker->uuid(),
            'transaction_id' => (UnionBank::$transactionModel)::factory(),
            'processing_item_id' => (UnionBank::$processingItemModel)::factory(),
            'union_bank_sender_id' => Sender::factory(),
            'union_bank_recipient_id' => Recipient::factory(),
            'state_code' => TransactionStateCodeEnum::LOCAL_UNPROCESSED,
            'reference' => $this->faker->uuid(),
            'receive_currency_code' => $this->faker->currencyCode(),
            'receive_amount' => $this->faker->randomFloat(2, 1),
        ];
    }
}
