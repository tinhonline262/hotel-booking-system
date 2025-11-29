<?php

namespace App\Infrastructure\DIContainer;

use App\Core\Container\Container;
use App\Core\Database\Database;
use App\Domain\Interfaces\Repositories\AdminRepositoryInterface;
use App\Domain\Interfaces\Repositories\RoomRepositoryInterface;
use App\Domain\Interfaces\Repositories\RoomImageRepositoryInterface;
use App\Domain\Interfaces\Repositories\RoomTypeRepositoryInterface;
use App\Domain\Interfaces\Repositories\BookingRepositoryInterface;
use App\Domain\Interfaces\Services\StorageConfigInterface;
use App\Infrastructure\Persistence\Repositories\AdminRepository;
use App\Infrastructure\Persistence\Repositories\RoomRepository;
use App\Infrastructure\Persistence\Repositories\RoomImageRepository;
use App\Infrastructure\Persistence\Repositories\RoomTypeRepository;
use App\Infrastructure\Persistence\Repositories\BookingRepository;
use App\Infrastructure\Persistence\Repositories\StorageConfigRepository;

/**
 * Repository Provider - Register all repository implementations
 */
class RepositoryProvider
{
    public static function register(Container $container): void
    {
        // Admin Repository
        $container->singleton(AdminRepositoryInterface::class, function (Container $c) {
            return new AdminRepository($c->make(Database::class));
        });

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

        // Room Repository
        $container->singleton(RoomRepositoryInterface::class, function (Container $c) {
            return new RoomRepository($c->make(Database::class));
        });

        // Booking Repository
        $container->singleton(BookingRepositoryInterface::class, function (Container $c) {
            return new BookingRepository($c->make(Database::class));
        });
    }
}