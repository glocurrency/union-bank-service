<?php

namespace GloCurrency\UnionBank\Tests\Feature\Models;

use Illuminate\Support\Facades\Event;
use GloCurrency\UnionBank\Tests\FeatureTestCase;
use GloCurrency\UnionBank\Models\Transaction;
use GloCurrency\UnionBank\Models\Recipient;
use GloCurrency\UnionBank\Events\TransactionCreatedEvent;

class GetRecipientTest extends FeatureTestCase
{
    /** @test */
    public function it_can_get_recipient(): void
    {
        Event::fake([
            TransactionCreatedEvent::class,
        ]);

        $recipient = Recipient::factory()->create();

        $transaction = Transaction::factory()->create([
            'union_bank_recipient_id' => $recipient->id,
        ]);

        $this->assertSame($recipient->id, $transaction->fresh()->recipient->id);
    }
}
