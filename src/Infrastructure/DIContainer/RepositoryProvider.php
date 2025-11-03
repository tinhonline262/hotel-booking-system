<?php

namespace App\Infrastructure\DIContainer;

use App\Core\Container\Container;
use App\Core\Database\Database;
use App\Domain\Interfaces\Repositories\RoomTypeRepositoryInterface;
use App\Infrastructure\Persistence\Repositories\RoomTypeRepository;

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
        // BookingRepositoryInterface => BookingRepository
        // RoomRepositoryInterface => RoomRepository
    }
}
