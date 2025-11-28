<?php

namespace App\Infrastructure\DIContainer;

use App\Core\Container\Container;
use App\Infrastructure\Services\ImageUploadFacade;

/**
 * App Service Provider - Main provider that registers all sub-providers
 * This is the entry point for the dependency injection system
 */
class AppServiceProvider
{
    /**
     * Register all application service providers
     */
    public static function register(Container $container): void
    {
        // Register providers in order of dependency
        // 1. System dependencies (Database, Cache, Logger)
        SystemProvider::register($container);

        // 2. Validators
        ValidatorProvider::register($container);

        // 3. Repositories
        RepositoryProvider::register($container);

        // 4. Use Cases
        UseCaseProvider::register($container);

        // 5. Services
        ServiceProvider::register($container);

        // 6. Controllers
        ControllerProvider::register($container);


    }

    /**
     * Boot method for any post-registration logic
     */
    public static function boot(Container $container): void
    {
        // Add any bootstrapping logic here
        // e.g., Event listeners, Middleware registration, etc.
    }
}

/**
 * Minimal ControllerProvider stub to satisfy references from AppServiceProvider.
 * Implement the actual registration logic in the real provider when available.
 */
class ControllerProvider
{
    /**
     * Register controller dependencies into the container.
     *
     * @param Container $container
     */
    public static function register(Container $container): void
    {
        // Intentionally left blank as a stub; replace with real registrations.
    }
}

