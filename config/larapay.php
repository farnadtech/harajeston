<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Gateway
    |--------------------------------------------------------------------------
    |
    | This option defines the default payment gateway that gets used when
    | no gateway is specified.
    |
    */
    'default' => env('LARAPAY_DEFAULT_GATEWAY', 'zarinpal'),

    /*
    |--------------------------------------------------------------------------
    | Payment Gateways
    |--------------------------------------------------------------------------
    |
    | Here you may configure the payment gateways. The credentials will be
    | loaded dynamically from the database via PaymentGatewayService.
    |
    */
    'gateways' => [
        'zarinpal' => [
            'merchant_id' => env('ZARINPAL_MERCHANT_ID', ''),
            'sandbox' => env('ZARINPAL_SANDBOX', false),
            'description' => 'پرداخت از طریق زرین‌پال',
        ],

        'zibal' => [
            'merchant_id' => env('ZIBAL_MERCHANT_ID', ''),
            'description' => 'پرداخت از طریق زیبال',
        ],

        'vandar' => [
            'api_key' => env('VANDAR_API_KEY', ''),
            'description' => 'پرداخت از طریق وندار',
        ],

        'payping' => [
            'api_key' => env('PAYPING_API_KEY', ''),
            'description' => 'پرداخت از طریق پی‌پینگ',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    |
    | The currency for all transactions. Default is IRR (Iranian Rial).
    | Note: Most Iranian gateways work with Toman, so amounts should be
    | divided by 10 before sending to gateway.
    |
    */
    'currency' => 'IRR',

    /*
    |--------------------------------------------------------------------------
    | Callback Route
    |--------------------------------------------------------------------------
    |
    | The route name for payment callback. This will be used to generate
    | the callback URL for payment gateways.
    |
    */
    'callback_route' => 'wallet.payment.callback',

    /*
    |--------------------------------------------------------------------------
    | Table Names
    |--------------------------------------------------------------------------
    |
    | If you want to use custom table names for storing payment transactions,
    | you can define them here.
    |
    */
    'table' => 'wallet_transactions',
];
