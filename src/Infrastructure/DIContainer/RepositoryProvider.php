<?php

namespace App\Infrastructure\DIContainer;

use App\Core\Container\Container;
use App\Core\Database\Database;
use App\Domain\Interfaces\Repositories\RoomRepositoryInterface;
use App\Domain\Interfaces\Repositories\RoomTypeRepositoryInterface;
use App\Domain\Interfaces\Repositories\BookingRepositoryInterface;
use App\Infrastructure\Persistence\Repositories\RoomRepository;
use App\Infrastructure\Persistence\Repositories\RoomTypeRepository;
use App\Infrastructure\Persistence\Repositories\BookingRepository;

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

        // Add more repositories here
        // UserRepositoryInterface => UserRepository
        // BookingRepositoryInterface => BookingRepositoryInterface
        // RoomRepositoryInterface => RoomRepository

        $container->singleton(RoomRepositoryInterface::class, function (Container $c) {
            return new RoomRepository($c->make(Database::class));
        });

        $container->singleton(BookingRepositoryInterface::class, function (Container $c) {
            return new BookingRepository($c->make(Database::class));
        });
    }
}
