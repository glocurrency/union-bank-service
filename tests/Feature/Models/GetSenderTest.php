<?php

namespace GloCurrency\UnionBank\Tests\Feature\Models;

use Illuminate\Support\Facades\Event;
use GloCurrency\UnionBank\Tests\FeatureTestCase;
use GloCurrency\UnionBank\Models\Transaction;
use GloCurrency\UnionBank\Models\Sender;

class GetSenderTest extends FeatureTestCase
{
    /** @test */
    public function it_can_get_sender(): void
    {
        Event::fake([
            TransactionCreatedEvent::class,
        ]);

        $sender = Sender::factory()->create();

        $transaction = Transaction::factory()->create([
            'union_bank_sender_id' => $sender->id,
        ]);

        $this->assertSame($sender->id, $transaction->fresh()->sender->id);
    }
}
