<?php

return [
    [
        'key'    => 'sales.payment_methods.azampay',
        'info'   => 'azampay::app.azampay.info',
        'name'   => 'AzamPay',
        'sort'   => 0,
        'fields' => [
            [
                'name'          => 'title',
                'title'         => 'azampay::app.azampay.system.title',
                'type'          => 'text',
                'depend'        => 'active:1',
                'validation'    => 'required_if:active,1',
                'channel_based' => false,
                'locale_based'  => true,
            ], [
                'name'          => 'description',
                'title'         => 'azampay::app.azampay.system.description',
                'type'          => 'textarea',
                'channel_based' => false,
                'locale_based'  => true,
            ], [
                'name'          => 'client_id',
                'title'         => 'azampay::app.azampay.system.client-id',
                'info'          => 'azampay::app.azampay.system.client-id-info',
                'type'          => 'text',
                'depend'        => 'active:1',
                'validation'    => 'required_if:active,1',
                'channel_based' => false,
                'locale_based'  => true,
            ], [
                'name'          => 'client_secret',
                'title'         => 'azampay::app.azampay.system.client-secret',
                'info'          => 'azampay::app.azampay.system.client-secret-info',
                'type'          => 'password',
                'depend'        => 'active:1',
                'validation'    => 'required_if:active,1',
                'channel_based' => false,
                'locale_based'  => true,
            ], [
                'name'          => 'image',
                'title'         => 'azampay::app.azampay.system.image',
                'info'          => 'admin::app.configuration.index.sales.payment-methods.logo-information',
                'type'          => 'file',
                'channel_based' => false,
                'locale_based'  => true,
            ], [
                'name'          => 'active',
                'title'         => 'azampay::app.azampay.system.status',
                'type'          => 'boolean',
                'validation'    => 'required',
                'channel_based' => false,
                'locale_based'  => true,
            ],
        ],
    ],
];
