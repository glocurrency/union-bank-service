<?php

namespace GloCurrency\UnionBank\Helpers;

use Illuminate\Support\Facades\Config;
use GloCurrency\UnionBank\Models\Recipient;
use GloCurrency\MiddlewareBlocks\Contracts\RecipientInterface as MRecipientInterface;

class RecipientFactory
{
    public static function makeFrom(MRecipientInterface $recipient): Recipient
    {
        return new Recipient([
            'first_name' => $recipient->getFirstName(),
            'last_name' => $recipient->getLastName(),
            'bank_code' => $recipient->getBankCode(),
            'bank_account' => $recipient->getBankAccount(),
            'phone_number' => $recipient->getPhoneNumber() ?? (string) Config::get('services.union_bank.recipient_phone_number'),
            'email' => $recipient->getEmail() ?? (string) Config::get('services.union_bank.recipient_email'),
            'country_code' => $recipient->getCountryCode(),
        ]);
    }
}
