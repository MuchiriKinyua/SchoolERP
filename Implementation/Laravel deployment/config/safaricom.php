<?php

    return [
        'consumer_key' => env('MPESA_CONSUMER_KEY', ''),
        'consumer_secret' => env('MPESA_CONSUMER_SECRET', ''),
        'env' => env('MPESA_ENV', 'sandbox'),
        'shortcode' => env('MPESA_SHORTCODE', ''),
        'passkey' => env('MPESA_PASSKEY', ''),
        'callback_url' => env('MPESA_CALLBACK_URL', ''),
    ];
    