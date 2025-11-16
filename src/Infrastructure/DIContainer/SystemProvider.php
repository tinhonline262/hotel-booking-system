<?php

namespace App\Infrastructure\DIContainer;

use App\Core\Container\Container;
use App\Core\Database\Database;

class SystemProvider
{
    private static array $providers = [
        ControllerProvider::class,
        RepositoryProvider::class,
        ServiceProvider::class,
        UseCaseProvider::class,
        ValidatorProvider::class,
        DashboardProvider::class,
    ];

    public static function register(Container $container): void 
    {
        // Database singleton
        $container->singleton(Database::class, function () {
            $config = require __DIR__ . '/../../../config/database.php';
            return Database::getInstance($config);
        });

        // Register all providers
        foreach (self::$providers as $providerClass) {
            $provider = new $providerClass();
            $provider->register($container);
        }
    }
}