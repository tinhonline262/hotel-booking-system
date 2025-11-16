<?php

namespace App\Infrastructure\DIContainer;

use App\Core\Container\Container;
use App\Core\Database\Database;
use App\Domain\Interfaces\Repositories\RoomImageRepositoryInterface;
use App\Domain\Interfaces\Repositories\RoomTypeRepositoryInterface;
use App\Domain\Interfaces\Services\StorageConfigInterface;
use App\Infrastructure\Persistence\Repositories\RoomImageRepository;
use App\Infrastructure\Persistence\Repositories\RoomTypeRepository;
use App\Infrastructure\Persistence\Repositories\StorageConfigRepository;

/**
 * Repository Provider - Register all repository implementations
 */
class RepositoryProvider
{
    public static function register(Container $container): void
    {
        // RoomType Repository
        $container->singleton(RoomTypeRepositoryInterface::class, function (Container $c) {
            return new RoomTypeRepository($c->make(Database::class));
        });

        // RoomImage Repository
        $container->singleton(RoomImageRepositoryInterface::class, function (Container $c) {
            return new RoomImageRepository($c->make(Database::class));
        });

        // Storage Config Repository
        $container->singleton(StorageConfigInterface::class, function (Container $c) {
            return new StorageConfigRepository($c->make(Database::class));
        });

        // Add more repositories here
        // UserRepositoryInterface => UserRepository
        // BookingRepositoryInterface => BookingRepository
        // RoomRepositoryInterface => RoomRepository
    }
}
