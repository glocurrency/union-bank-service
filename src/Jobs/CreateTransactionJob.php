<?php

namespace GloCurrency\UnionBank\Jobs;

use Money\Formatter\DecimalMoneyFormatter;
use Illuminate\Support\Facades\App;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Bus\Queueable;
use GloCurrency\UnionBank\Models\Transaction;
use GloCurrency\UnionBank\Helpers\SenderFactory;
use GloCurrency\UnionBank\Helpers\RecipientFactory;
use GloCurrency\UnionBank\Exceptions\CreateTransactionException;
use GloCurrency\UnionBank\Enums\TransactionStateCodeEnum;
use GloCurrency\MiddlewareBlocks\Enums\TransactionTypeEnum as MTransactionTypeEnum;
use GloCurrency\MiddlewareBlocks\Enums\TransactionStateCodeEnum as MTransactionStateCodeEnum;
use GloCurrency\MiddlewareBlocks\Enums\QueueTypeEnum as MQueueTypeEnum;
use GloCurrency\MiddlewareBlocks\Enums\ProcessingItemStateCodeEnum as MProcessingItemStateCodeEnum;
use GloCurrency\MiddlewareBlocks\Contracts\ProcessingItemInterface as MProcessingItemInterface;

class CreateTransactionJob implements ShouldQueue, ShouldBeUnique, ShouldBeEncrypted
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;

    private MProcessingItemInterface $processingItem;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(MProcessingItemInterface $processingItem)
    {
        $this->processingItem = $processingItem;
        $this->afterCommit();
        $this->onQueue(MQueueTypeEnum::SERVICES->value);
    }

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
        return $this->processingItem->getId();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $transaction = $this->processingItem->getTransaction();

        if (!$transaction) {
            throw CreateTransactionException::noTransaction($this->processingItem);
        }

        if (MTransactionTypeEnum::BANK !== $transaction->getType()) {
            throw CreateTransactionException::typeNotAllowed($transaction);
        }

        if (MTransactionStateCodeEnum::PROCESSING !== $transaction->getStateCode()) {
            throw CreateTransactionException::stateNotAllowed($transaction);
        }

        /** @var Transaction|null $targetTransaction */
        $targetTransaction = Transaction::firstWhere('transaction_id', $transaction->getId());

        if ($targetTransaction) {
            throw CreateTransactionException::duplicateTargetTransaction($targetTransaction);
        }

        $transactionSender = $transaction->getSender();

        if (!$transactionSender) {
            throw CreateTransactionException::noTransactionSender($transaction);
        }

        $transactionRecipient = $transaction->getRecipient();

        if (!$transactionRecipient) {
            throw CreateTransactionException::noTransactionRecipient($transaction);
        }

        if (!$transactionRecipient->getBankCode()) {
            throw CreateTransactionException::noBankCode($transactionRecipient);
        }

        if (!$transactionRecipient->getBankAccount()) {
            throw CreateTransactionException::noBankAccount($transactionRecipient);
        }


        $targetSender = SenderFactory::makeFrom($transactionSender);
        $targetSender->save();

        $targetRecipient = RecipientFactory::makeFrom($transactionRecipient);
        $targetRecipient->save();

        /** @var DecimalMoneyFormatter $moneyFormatter */
        $moneyFormatter = App::make(DecimalMoneyFormatter::class);

        Transaction::create([
            'transaction_id' => $transaction->getId(),
            'processing_item_id' => $this->processingItem->getId(),
            'union_bank_sender_id' => $targetSender->id,
            'union_bank_recipient_id' => $targetRecipient->id,
            'state_code' => TransactionStateCodeEnum::LOCAL_UNPROCESSED,
            'reference' => $transaction->getReferenceForHumans(),
            'receive_currency_code' => $transaction->getOutputAmount()->getCurrency()->getCode(),
            'receive_amount' => $moneyFormatter->format($transaction->getOutputAmount()),
        ]);
    }

    /**
     * Handle a job failure.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(\Throwable $exception)
    {
        report($exception);

        if ($exception instanceof CreateTransactionException) {
            $this->processingItem->updateStateCode($exception->getStateCode(), $exception->getStateCodeReason());
            return;
        }

        $this->processingItem->updateStateCode(MProcessingItemStateCodeEnum::EXCEPTION, $exception->getMessage());
    }
}
