<?php

return [

    'polygon' => [
        'merchant_id' => env('PAYRA_POLYGON_MERCHANT_ID'),
        'private_key' => env('PAYRA_POLYGON_PRIVATE_KEY'),
    ],

    'ethereum' => [
        'merchant_id' => env('PAYRA_ETHEREUM_MERCHANT_ID'),
        'private_key' => env('PAYRA_ETHEREUM_PRIVATE_KEY'),
    ],

];
