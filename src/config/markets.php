<?php

return [
    'adapter' => [
        'type' => env('MARKET_ADAPTER_TYPE', 'real'),
        'urls' => [
            'android' => [
                'mock' => env('MARKET_URLS_ANDROID_MOCK', 'http://play-test.google.com'),
                'real' => env('MARKET_URLS_ANDROID_REAL', 'http://play.google.com'),
            ],
            'ios'     => [
                'mock' => env('MARKET_URLS_IOS_MOCK', 'http://store-test.apple.com'),
                'real' => env('MARKET_URLS_IOS_REAL', 'http://store.apple.com')
            ],
        ]
    ]
];
