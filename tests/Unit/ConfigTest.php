<?php

namespace GloCurrency\UnionBank\Tests\Unit;

use Illuminate\Foundation\Testing\WithFaker;
use GloCurrency\UnionBank\Tests\TestCase;
use GloCurrency\UnionBank\Config;
use BrokeYourBike\UnionBank\Interfaces\ConfigInterface;

class ConfigTest extends TestCase
{
    use WithFaker;

    /** @test */
    public function it_implemets_config_interface(): void
    {
        $this->assertInstanceOf(ConfigInterface::class, new Config());
    }

    /** @test */
    public function it_will_return_empty_string_if_value_not_found()
    {
        $configPrefix = 'services.union_bank.api';

        // config is empty
        config([$configPrefix => []]);

        $config = new Config();

        $this->assertSame('', $config->getUrl());
        $this->assertSame('', $config->getAuthUrl());
        $this->assertSame('', $config->getUsername());
        $this->assertSame('', $config->getPassword());
        $this->assertSame('', $config->getMerchantCode());
    }

    /** @test */
    public function it_can_return_values()
    {
        $url = $this->faker->url;
        $authUrl = $this->faker->url;
        $username = $this->faker->userName;
        $password = $this->faker->password();
        $merchantCode = $this->faker->word();

        $configPrefix = 'services.union_bank.api';

        config(["{$configPrefix}.url" => $url]);
        config(["{$configPrefix}.auth_url" => $authUrl]);
        config(["{$configPrefix}.username" => $username]);
        config(["{$configPrefix}.password" => $password]);
        config(["{$configPrefix}.merchant_code" => $merchantCode]);

        $config = new Config();

        $this->assertSame($url, $config->getUrl());
        $this->assertSame($authUrl, $config->getAuthUrl());
        $this->assertSame($username, $config->getUsername());
        $this->assertSame($password, $config->getPassword());
        $this->assertSame($merchantCode, $config->getMerchantCode());
    }
}
