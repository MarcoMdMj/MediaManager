<?php

return [
    
    // Path where media is stored.
    'path' => storage_path('app/public/media'),

    // Supported mimetypes and the associated extension.
    'mimetypes' => [
        'image/png' => 'png',
        'image/jpeg' => 'jpg',
        'image/gif' => 'gif',
        'image/bmp' => 'bmp',
        'image/svg+xml' => 'svg'
    ],

    // Laravel storage disk used for managing files.
    'disk' => 'media',

    // Suffix for filenames.
    'suffix' => '@%Y%b%dT%H%M%S' // ie. @2016Dec10T154537
];