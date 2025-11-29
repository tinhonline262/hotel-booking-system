<?php

return [
    'driver' => 'mysql', // mysql, pgsql, sqlite
    'host' =>'localhost',
    'port' => '3307',
    'database' => 'hotel_booking_system',
    'username' => 'an',
    'password' => 'napui@123',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ],
];

