<?php

return [

    'api_key' => env('PAYRA_API_KEY', ''),

    'exchange_rate_api_key' => env('PAYRA_EXCHANGE_RATE_API_KEY', ''),
    'exchange_rate_cache_time' => env('PAYRA_EXCHANGE_RATE_CACHE_TIME', '720'),

    'polygon' => [
        'merchant_id' => env('PAYRA_POLYGON_MERCHANT_ID'),
        'private_key' => env('PAYRA_POLYGON_PRIVATE_KEY'),
        'rpc_urls' => [],
    ],

    'ethereum' => [
        'merchant_id' => env('PAYRA_ETHEREUM_MERCHANT_ID'),
        'private_key' => env('PAYRA_ETHEREUM_PRIVATE_KEY'),
        'rpc_urls' => [],
    ],

];
