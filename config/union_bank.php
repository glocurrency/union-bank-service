<?php

return [
    'api' => [
        'url' => env('UNION_BANK_API_URL'),
        'auth_url' => env('UNION_BANK_API_AUTH_URL'),
        'username' => env('UNION_BANK_API_USERNAME'),
        'password' => env('UNION_BANK_API_PASSWORD'),
        'merchant_code' => env('UNION_BANK_API_MERCHANT_CODE'),
    ],
    'sender_phone_number' => env('UNION_BANK_SENDER_PHONE_NUMBER'),
    'sender_bank_account' => env('UNION_BANK_SENDER_BANK_ACCOUNT'),
    'sender_bank_code' => env('UNION_BANK_SENDER_BANK_CODE'),
    'recipient_phone_number' => env('UNION_BANK_RECIPIENT_PHONE_NUMBER'),
    'recipient_email' => env('UNION_BANK_RECIPIENT_EMAIL'),
];
