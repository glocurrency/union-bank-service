<?php

namespace GloCurrency\UnionBank\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use GloCurrency\UnionBank\UnionBank;
use GloCurrency\UnionBank\Events\TransactionUpdatedEvent;
use GloCurrency\UnionBank\Events\TransactionCreatedEvent;
use GloCurrency\UnionBank\Enums\TransactionStateCodeEnum;
use GloCurrency\UnionBank\Database\Factories\TransactionFactory;
use GloCurrency\MiddlewareBlocks\Contracts\ModelWithStateCodeInterface;
use BrokeYourBike\UnionBank\Interfaces\TransactionInterface;
use BrokeYourBike\UnionBank\Interfaces\SenderInterface;
use BrokeYourBike\UnionBank\Interfaces\RecipientInterface;
use BrokeYourBike\UnionBank\Enums\ErrorCodeEnum;
use BrokeYourBike\HasSourceModel\SourceModelInterface;
use BrokeYourBike\BaseModels\BaseUuid;

/**
 * GloCurrency\UnionBank\Models\Transaction
 *
 * @property string $id
 * @property string $transaction_id
 * @property string $processing_item_id
 * @property string $union_bank_sender_id
 * @property string $union_bank_recipient_id
 * @property \GloCurrency\UnionBank\Enums\TransactionStateCodeEnum $state_code
 * @property string|null $state_code_reason
 * @property \BrokeYourBike\UnionBank\Enums\ErrorCodeEnum|null $error_code
 * @property string|null $error_code_description
 * @property string $reference
 * @property string $remote_reference
 * @property string $receive_currency_code
 * @property float $receive_amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class Transaction extends BaseUuid implements ModelWithStateCodeInterface, SourceModelInterface, TransactionInterface
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'union_bank_transactions';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<mixed>
     */
    protected $casts = [
        'state_code' => TransactionStateCodeEnum::class,
        'error_code' => ErrorCodeEnum::class,
        'receive_amount' => 'double',
    ];

    /**
     * @var array<mixed>
     */
    protected $dispatchesEvents = [
        'created' => TransactionCreatedEvent::class,
        'updated' => TransactionUpdatedEvent::class,
    ];

    public function getStateCode(): TransactionStateCodeEnum
    {
        return $this->state_code;
    }

    public function getStateCodeReason(): ?string
    {
        return $this->state_code_reason;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function getRemoteReference(): string
    {
        return $this->remote_reference;
    }

    public function getCurrencyCode(): string
    {
        return $this->receive_currency_code;
    }

    public function getAmount(): float
    {
        return $this->receive_amount;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->created_at ?? now();
    }

    public function getSender(): ?SenderInterface
    {
        return $this->sender;
    }

    public function getRecipient(): ?RecipientInterface
    {
        return $this->recipient;
    }

    /**
     * The ProcessingItem that Transaction belong to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function processingItem()
    {
        return $this->belongsTo(UnionBank::$processingItemModel, 'processing_item_id', 'id');
    }

    /**
     * The Recipient that Transaction has.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function recipient()
    {
        return $this->hasOne(Recipient::class, 'id', 'union_bank_recipient_id');
    }

    /**
     * The Sender that Transaction has.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function sender()
    {
        return $this->hasOne(Sender::class, 'id', 'union_bank_sender_id');
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return TransactionFactory::new();
    }
}
