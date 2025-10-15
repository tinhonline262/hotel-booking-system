<?php

return [
    'app_name' => 'Hotel Booking System',
    'app_url' => 'http://localhost',
    'environment' => 'development', // development, production
    'debug' => true,
    'timezone' => 'UTC',

    'paths' => [
        'views' => __DIR__ . '/../src/Presentation/Views',
        'storage' => __DIR__ . '/../storage',
        'cache' => __DIR__ . '/../storage/cache',
        'logs' => __DIR__ . '/../storage/logs',
        'sessions' => __DIR__ . '/../storage/sessions',
        'uploads' => __DIR__ . '/../public/uploads',
    ],
];

