<?php

namespace GloCurrency\UnionBank\Helpers;

use Illuminate\Support\Facades\Config;
use GloCurrency\UnionBank\Models\Sender;
use GloCurrency\MiddlewareBlocks\Contracts\SenderInterface as MSenderInterface;

class SenderFactory
{
    public static function makeFrom(MSenderInterface $sender): Sender
    {
        return new Sender([
            'first_name' => $sender->getFirstName(),
            'last_name' => $sender->getLastName(),
            'phone_number' => $sender->getPhoneNumber() ?? (string) Config::get('services.union_bank.sender_phone_number'),
            'bank_account' => (string) Config::get('services.union_bank.sender_bank_account'),
            'bank_code' => (string) Config::get('services.union_bank.sender_bank_code'),
            'country_code' => $sender->getCountryCode(),
        ]);
    }
}
