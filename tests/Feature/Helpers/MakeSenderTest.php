<?php

namespace GloCurrency\UnionBank\Tests\Feature\Helpers;

use GloCurrency\UnionBank\Tests\FeatureTestCase;
use GloCurrency\UnionBank\Models\Sender;
use GloCurrency\UnionBank\Helpers\SenderFactory;
use GloCurrency\MiddlewareBlocks\Contracts\SenderInterface as MSenderInterface;

class MakeSenderTest extends FeatureTestCase
{
    /** @test */
    public function it_can_make_sender(): void
    {
        $sender = $this->getMockBuilder(MSenderInterface::class)->getMock();
        $sender->method('getFirstName')->willReturn($this->faker->firstName());
        $sender->method('getLastName')->willReturn($this->faker->lastName());
        $sender->method('getCountryCode')->willReturn($this->faker->countryISOAlpha3());
        $sender->method('getPhoneNumber')->willReturn($this->faker->phoneNumber());

        /** @var MSenderInterface $sender */
        $targetSender = SenderFactory::makeFrom($sender);

        $this->assertInstanceOf(Sender::class, $targetSender);
        $this->assertSame($sender->getFirstName(), $targetSender->first_name);
        $this->assertSame($sender->getLastName(), $targetSender->last_name);
        $this->assertEquals($sender->getCountryCode(), $targetSender->country_code);
        $this->assertEquals($sender->getPhoneNumber(), $targetSender->phone_number);

        // TODO: bank_code and bank_account from config should be tested
    }
}
