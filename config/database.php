<?php

return [
    'driver' => 'mysql', // mysql, pgsql, sqlite
    'host' => 'yamabiko.proxy.rlwy.net',
    'port' => '15242',
    'database' => 'hotel_booking_system',
    'username' => 'root',
    'password' => 'meXceRwzglYiFRtopuXukOPfzrbtdblT',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ],
];

