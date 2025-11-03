<?php

namespace App\Infrastructure\DIContainer;

use App\Core\Container\Container;
use App\Core\Database\Database;

/**
 * System Provider - Register core system dependencies
 * Database, Cache, Logger, etc.
 */
class SystemProvider
{
    public static function register(Container $container): void
    {
        // Database singleton
        $container->singleton(Database::class, function () {
            $config = require __DIR__ . '/../../../config/database.php';
            return Database::getInstance($config);
        });

        // Add other system dependencies here
        // Cache, Logger, FileSystem, etc.
    }
}

