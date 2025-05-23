<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Broadcasting
    |--------------------------------------------------------------------------
    */
    /*
    'broadcasting' => [
        'echo' => [
            'broadcaster' => env('BROADCAST_DRIVER', 'pusher'),
            'key' => env('PUSHER_APP_KEY'),
            'cluster' => env('PUSHER_APP_CLUSTER'),
            'wsHost' => env('PUSHER_HOST'),
            'wsPort' => env('PUSHER_PORT', 443),
            'wssPort' => env('PUSHER_PORT', 443),
            'authEndpoint' => '/broadcasting/auth',
            'disableStats' => true,
            'encrypted' => true,
        ],
    ],
*/
    /*
    |--------------------------------------------------------------------------
    | Database Notifications
    |--------------------------------------------------------------------------
    */
    'database_notifications' => [
        'enabled' => true,
        'polling_interval' => '30s',
        'trigger' => 'filament.database-notifications.trigger',
    ],

    /*
    |--------------------------------------------------------------------------
    | Assets Path
    |--------------------------------------------------------------------------
    */
    'assets_path' => 'app/filament',

    /*
    |--------------------------------------------------------------------------
    | Cache Path
    |--------------------------------------------------------------------------
    */
    'cache_path' => base_path('bootstrap/cache/filament'),

    /*
    |--------------------------------------------------------------------------
    | Livewire
    |--------------------------------------------------------------------------
    */
    'livewire' => [
        'enabled' => false, // Désactivé pour éviter Livewire
    ],
];
