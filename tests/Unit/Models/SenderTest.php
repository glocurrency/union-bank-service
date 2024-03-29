<?php

namespace GloCurrency\UnionBank\Tests\Unit\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use GloCurrency\UnionBank\Tests\TestCase;
use GloCurrency\UnionBank\Models\Sender;
use BrokeYourBike\BaseModels\BaseUuid;

class SenderTest extends TestCase
{
    /** @test */
    public function it_extends_base_model(): void
    {
        $parent = get_parent_class(Sender::class);

        $this->assertSame(BaseUuid::class, $parent);
    }

    /** @test */
    public function it_uses_soft_deletes(): void
    {
        $usedTraits = class_uses(Sender::class);

        $this->assertArrayHasKey(SoftDeletes::class, $usedTraits);
    }

    /** @test */
    public function it_can_return_country_code_alpha2()
    {
        $sender = new Sender();
        $sender->country_code = 'USA';

        $this->assertSame('US', $sender->country_code_alpha2);
    }
}
