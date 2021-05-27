<?php

return [
    'default-disk' => 'attachments',
    'disk' => [
        'driver' => 'local',
        'root' => storage_path('app/public/attachments'),
        'url' => env('APP_URL') . '/storage/attachments',
        'visibility' => 'public',
    ]
];
