<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Routing
    |--------------------------------------------------------------------------
    */
    'use_package_routes' => true,
    'middlewares' => ['web', 'auth'],
    'url_prefix' => 'laravel-filemanager',

    /*  
    |-------------------------------------------------------------------------
    | Auto Callbacks
    |-------------------------------------------------------------------------  
    */
     'allow_multi_selection' => false,
    /*
    |--------------------------------------------------------------------------
    | Folder Access
    |--------------------------------------------------------------------------
    */
    'allow_private_folder' => true,
    'allow_shared_folder' => false,
    'shared_folder_name'   => null,

    /*
    |--------------------------------------------------------------------------
    | Private Folder Handler
    |--------------------------------------------------------------------------
    | Kita override agar folder = audios/{package_id}
    */
    'handler' => App\Handlers\ConfigHandler::class,

    /*
    |--------------------------------------------------------------------------
    | Folder Categories (MULTI RESOURCE)
    |--------------------------------------------------------------------------
    */
    'folder_categories' => [

        /*
        | AUDIO – Listening TOEFL
        */
        'audio' => [
            'folder_name' => 'audios',
            'startup_view' => 'list',
            'max_size' => 204800, // 200MB
            'valid_mime' => [
                'audio/mpeg',
                'audio/mp3',
                'audio/wav',
                'audio/ogg',
                'video/mp4',
            ],
        ],

        /*
        | DOCUMENT – Sertifikat, PDF, dll
        */
        'file' => [
            'folder_name' => 'files',
            'startup_view' => 'list',
            'max_size' => 51200, // 50MB
            'valid_mime' => [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ],
        ],

        /*
        | IMAGE – Resource UI & Sertifikat
        */
        'image' => [
            'folder_name' => 'images',
            'startup_view' => 'grid',
            'max_size' => 51200, // 50MB
            'valid_mime' => [
                'image/jpeg',
                'image/png',
                'image/webp',
                'image/svg+xml',
            ],
        ],

        /*
        | VIDEO – Tutorial & Kelas
        */
        'video' => [
            'folder_name' => 'videos',
            'startup_view' => 'list',
            'max_size' => 512000, // 500MB
            'valid_mime' => [
                'video/mp4',
                'video/webm',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Upload Settings
    |--------------------------------------------------------------------------
    */
    'disk' => 'public',

    'rename_file' => false,
    'rename_duplicates' => false,

    'should_validate_size' => true,
    'should_validate_mime' => true,

    'over_write_on_duplicate' => false,

    /*
    |--------------------------------------------------------------------------
    | Security
    |--------------------------------------------------------------------------
    */
    'disallowed_mimetypes' => [
        'text/x-php',
        'text/html',
        'application/javascript',
    ],

    'disallowed_extensions' => [
        'php',
        'html',
        'js',
    ],

    /*
    |--------------------------------------------------------------------------
    | UI
    |--------------------------------------------------------------------------
    */
    'item_columns' => [
        'name',
        'url',
        'time',
        'icon',
        'is_file',
        'is_image',
        'thumb_url',
    ],

    'is_reverse_view' => false,

    /*
    |--------------------------------------------------------------------------
    | Thumbnail (IMAGE ONLY)
    |--------------------------------------------------------------------------
    */
    'should_create_thumbnails' => true,
    'thumb_folder_name' => 'thumbs',

    'raster_mimetypes' => [
        'image/jpeg',
        'image/png',
        'image/webp',
    ],

    'thumb_img_width' => 200,
    'thumb_img_height' => 200,

    /*
    |--------------------------------------------------------------------------
    | PHP Override
    |--------------------------------------------------------------------------
    */
    'php_ini_overrides' => [
        'memory_limit' => '512M',
    ],
];
