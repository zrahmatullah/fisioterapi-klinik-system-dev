<?php

return [

    'default' => env('REVERB_SERVER', 'reverb'),

    'servers' => [

        'reverb' => [
            'host' => env('REVERB_SERVER_HOST', '0.0.0.0'),
            'port' => env('REVERB_SERVER_PORT', 6001),
            'path' => env('REVERB_SERVER_PATH', ''),
            'hostname' => env('REVERB_HOST', '127.0.0.1'),
            'options' => [
                'tls' => [],
            ],
            'max_request_size' => env('REVERB_MAX_REQUEST_SIZE', 10_000),

            // Scaling OFF (biar tidak perlu Redis)
            'scaling' => [
                'enabled' => false,
            ],

            'pulse_ingest_interval' => 15,
            'telescope_ingest_interval' => 15,
        ],

    ],

    'apps' => [

        'provider' => 'config',

        'apps' => [
            [
                'app_id' => env('REVERB_APP_ID'),
                'key' => env('REVERB_APP_KEY'),
                'secret' => env('REVERB_APP_SECRET'),

                // 🔥 HARUS SAMA DENGAN SERVER
                'options' => [
                    'host' => env('REVERB_HOST', '127.0.0.1'),
                    'port' => env('REVERB_PORT', 6001),
                    'scheme' => env('REVERB_SCHEME', 'http'),
                    'useTLS' => false,
                ],

                'allowed_origins' => ['*'],

                // 🔥 WAJIB DI REVERB BARU
                'ping_interval' => 60,
                'activity_timeout' => 30,

                'max_connections' => null,
                'max_message_size' => 10_000,
            ],
        ],

    ],

];
