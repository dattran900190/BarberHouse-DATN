<?php

return [
    'default' => env('BROADCAST_CONNECTION', 'pusher'),
    
    'connections' => [
        'pusher' => [
            'driver' => 'pusher',
            'key' => env('PUSHER_APP_KEY'),
            'secret' => env('PUSHER_APP_SECRET'),
            'app_id' => env('PUSHER_APP_ID'),
            'options' => [
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'encrypted' => true,
                'host' => 'api-ap1.pusher.com',
                'port' => 443,
                'scheme' => 'https',
            ],
        ],
        
        'null' => [
            'driver' => 'null',
        ],
    ],
];