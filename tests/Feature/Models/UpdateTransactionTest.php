<?php

namespace GloCurrency\UnionBank\Tests\Feature\Models;

use Illuminate\Support\Facades\Event;
use GloCurrency\UnionBank\Tests\FeatureTestCase;
use GloCurrency\UnionBank\Models\Transaction;
use GloCurrency\UnionBank\Events\TransactionUpdatedEvent;

class UpdateTransactionTest extends FeatureTestCase
{
    /** @test */
    public function fire_event_when_it_updated(): void
    {
        $transaction = Transaction::factory()->create([
            'state_code_reason' => 'abc',
        ]);

        Event::fake();

        $transaction->state_code_reason = 'xyz';
        $transaction->save();

        Event::assertDispatched(TransactionUpdatedEvent::class);
    }
}
