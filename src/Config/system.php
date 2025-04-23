<?php

return [
    [
        'key'    => 'sales.payment_methods.azampay',
        'name'   => 'azampay',
        'info'   => 'azampay::azampay.info',
        'sort'   => 0,
        'fields' => [
            [
                'name'          => 'title',
                'title'         => 'azampay::azampay.system.title',
                'type'          => 'text',
                'validation'    => 'required_if:active,1',
                'depend'        => 'active:1',
                'channel_based' => false,
                'locale_based'  => true,
            ], [
                'name'          => 'description',
                'title'         => 'azampay::azampay.system.description',
                'type'          => 'textarea',
                'channel_based' => false,
                'locale_based'  => true,
            ], [
                'name'          => 'image',
                'title'         => 'azampay::azampay.system.image',
                'info'          => 'admin::app.configuration.index.sales.payment-methods.logo-information',
                'type'          => 'file',
                'channel_based' => false,
                'locale_based'  => true,
            ], [
                'name'          => 'azampay_client_id',
                'title'         => 'azampay::azampay.system.client-id',
                'info'          => 'azampay::azampay.system.client-id-info',
                'type'          => 'text',
                'validation'    => 'required_if:active,1',
                'depend'        => 'active:1',
                'channel_based' => false,
                'locale_based'  => true,
            ], [
                'name'          => 'azampay_api_key',
                'title'         => 'azampay::azampay.system.client-secret',
                'info'          => 'azampay::azampay.system.client-secret-info',
                'type'          => 'password',
                'validation'    => 'required_if:active,1',
                'depend'        => 'active:1',
                'channel_based' => false,
                'locale_based'  => true,
            ], [
                'name'          => 'azampay_currencies',
                'title'         => 'azampay::azampay.system.accepted-currencies',
                'type'          => 'text',
                'info'          => 'Add currency codes comma separated, e.g., USD, TZS',
                'channel_based' => false,
                'locale_based'  => true,
            ], [
                'name'          => 'active',
                'title'         => 'azampay::azampay.system.status',
                'type'          => 'boolean',
                'validation'    => 'required',
                'channel_based' => false,
                'locale_based'  => true,
            ], [
                'name'          => 'sandbox',
                'title'         => 'azampay::azampay.system.sandbox',
                'type'          => 'boolean',
                'info'          => 'Enable sandbox mode for testing transactions',
                'channel_based' => false,
                'locale_based'  => true,
            ], [
                'name'          => 'sort',
                'title'         => 'azampay::azampay.system.sort_order',
                'type'          => 'text',
                'channel_based' => false,
                'locale_based'  => true,
            ],
        ],
    ],
];
