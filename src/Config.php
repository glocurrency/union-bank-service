<?php

namespace GloCurrency\UnionBank;

use BrokeYourBike\UnionBank\Interfaces\ConfigInterface;

final class Config implements ConfigInterface
{
    private function getAppConfigValue(string $key): string
    {
        $value = \Illuminate\Support\Facades\Config::get("services.union_bank.api.$key");

        return is_string($value) ? $value : '';
    }

    public function getUrl(): string
    {
        return $this->getAppConfigValue('url');
    }

    public function getAuthUrl(): string
    {
        return $this->getAppConfigValue('auth_url');
    }

    public function getUsername(): string
    {
        return $this->getAppConfigValue('username');
    }

    public function getPassword(): string
    {
        return $this->getAppConfigValue('password');
    }

    public function getMerchantCode(): string
    {
        return $this->getAppConfigValue('merchant_code');
    }
}
