<?php

namespace GloCurrency\UnionBank\Listeners;

use GloCurrency\UnionBank\Jobs\SendTransactionJob;
use GloCurrency\UnionBank\Events\TransactionCreatedEvent;
use GloCurrency\UnionBank\Enums\TransactionStateCodeEnum;

class SendTransactionSubscriber
{
    /**
     * Indicates whether the job should be dispatched after all database transactions have committed.
     *
     * @var bool|null
     */
    public $afterCommit = true;

    /**
     * Handle TransactionCreatedEvent's.
     *
     * @param  TransactionCreatedEvent  $event
     * @return void
     */
    public function handleItemCreated(TransactionCreatedEvent $event)
    {
        if (TransactionStateCodeEnum::LOCAL_UNPROCESSED !== $event->transaction->state_code) {
            return;
        }

        SendTransactionJob::dispatch($event->transaction);
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     * @return void
     */
    public function subscribe($events)
    {
        $events->listen(
            TransactionCreatedEvent::class,
            [SendTransactionSubscriber::class, 'handleItemCreated']
        );
    }
}