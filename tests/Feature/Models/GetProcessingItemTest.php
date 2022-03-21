<?php

namespace GloCurrency\AccessBank\Tests\Feature\Models;

use Illuminate\Support\Facades\Event;
use GloCurrency\UnionBank\Tests\Fixtures\ProcessingItemFixture;
use GloCurrency\UnionBank\Tests\FeatureTestCase;
use GloCurrency\UnionBank\Models\Transaction;
use GloCurrency\UnionBank\Events\TransactionCreatedEvent;

class GetProcessingItemTest extends FeatureTestCase
{
    /** @test */
    public function it_can_get_processing_item(): void
    {
        Event::fake([
            TransactionCreatedEvent::class,
        ]);

        $processingItem = ProcessingItemFixture::factory()->create();

        $transaction = Transaction::factory()->create([
            'processing_item_id' => $processingItem->id,
        ]);

        $this->assertSame($processingItem->id, $transaction->fresh()->processingItem->id);
    }
}
