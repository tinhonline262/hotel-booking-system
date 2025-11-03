<?php

namespace App\Infrastructure\DIContainer;

use App\Application\UseCases\CreateRoomTypeUseCase;
use App\Application\UseCases\DeleteRoomTypeUseCase;
use App\Application\UseCases\FilterRoomTypesByCapacityUseCase;
use App\Application\UseCases\FilterRoomTypesByPriceRangeUseCase;
use App\Application\UseCases\GetAllRoomTypesUseCase;
use App\Application\UseCases\GetRoomTypeUseCase;
use App\Application\UseCases\UpdateRoomTypeUseCase;
use App\Application\Validators\RoomTypeValidator;
use App\Core\Container\Container;
use App\Domain\Interfaces\Repositories\RoomTypeRepositoryInterface;

/**
 * UseCase Provider - Register all use cases
 */
class UseCaseProvider
{
    public static function register(Container $container): void
    {
        // RoomType Use Cases
        self::registerRoomTypeUseCases($container);

        // Add more use case groups here
        // self::registerUserUseCases($container);
        // self::registerBookingUseCases($container);
    }

    private static function registerRoomTypeUseCases(Container $container): void
    {
        $container->bind(CreateRoomTypeUseCase::class, function (Container $c) {
            return new CreateRoomTypeUseCase(
                $c->make(RoomTypeRepositoryInterface::class),
                $c->make(RoomTypeValidator::class)
            );
        });

        $container->bind(GetRoomTypeUseCase::class, function (Container $c) {
            return new GetRoomTypeUseCase(
                $c->make(RoomTypeRepositoryInterface::class)
            );
        });

        $container->bind(GetAllRoomTypesUseCase::class, function (Container $c) {
            return new GetAllRoomTypesUseCase(
                $c->make(RoomTypeRepositoryInterface::class)
            );
        });

        $container->bind(UpdateRoomTypeUseCase::class, function (Container $c) {
            return new UpdateRoomTypeUseCase(
                $c->make(RoomTypeRepositoryInterface::class),
                $c->make(RoomTypeValidator::class)
            );
        });

        $container->bind(DeleteRoomTypeUseCase::class, function (Container $c) {
            return new DeleteRoomTypeUseCase(
                $c->make(RoomTypeRepositoryInterface::class)
            );
        });

        $container->bind(FilterRoomTypesByCapacityUseCase::class, function (Container $c) {
            return new FilterRoomTypesByCapacityUseCase(
                $c->make(RoomTypeRepositoryInterface::class)
            );
        });

        $container->bind(FilterRoomTypesByPriceRangeUseCase::class, function (Container $c) {
            return new FilterRoomTypesByPriceRangeUseCase(
                $c->make(RoomTypeRepositoryInterface::class)
            );
        });
    }
}

