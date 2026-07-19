<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been set up for each driver as an example of the required values.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [
        'temp' => [
            'driver' => 'local',
            'root' => base_path(). sys_get_temp_dir(),
            'visibility' => 'private',
            'throw' => true,
        ],
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],
        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],

        'public_profile_image' => [
            'driver' => 'local',
            'root' => storage_path('/app/public/profile/image/'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],
        'public_feed_file' => [
            'driver' => 'local',
            'root' => storage_path('/app/public/feed/image/'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],
        'public_daily_usage_image' => [
            'driver' => 'local',
            'root' => storage_path('/app/public/account/daily-usage-image/'),
            'url' => env('APP_URL').'/storage/account/daily-usage-image',
            'visibility' => 'public',
            'throw' => false,
        ],
        'public_telegram_profile_images' => [
            'driver' => 'local',
            'root' => storage_path('app/public/telegram/profile-images'),
            'url' => env('APP_URL').'/storage/telegram/profile-images',
            'visibility' => 'public',
            'throw' => false,
        ],
        'public_telegram_chat_images' => [
            'driver' => 'local',
            'root' => storage_path('app/public/telegram/chat-images'),
            'url' => env('APP_URL').'/storage/telegram/chat-images',
            'visibility' => 'public',
            'throw' => false,
        ],
        'private_telegram_session' => [
            'driver' => 'local',
            'root' => storage_path('/app/private/telegram/session/'),
            'visibility' => 'private',
            'throw' => false,
        ],
        'public_export_download_dir' => [
            'driver' => 'local',
            'root' => storage_path('/app/public/export/download/'),
            'url' => env('APP_URL').'/storage/export/download',
            'visibility' => 'public',
            'throw' => false,
        ],
        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
