<?php

namespace App\Infrastructure\DIContainer;

use App\Application\UseCases\CreateRoomTypeUseCase;
use App\Application\UseCases\DeleteRoomTypeUseCase;
use App\Application\UseCases\FilterRoomTypesByCapacityUseCase;
use App\Application\UseCases\FilterRoomTypesByPriceRangeUseCase;
use App\Application\UseCases\GetAllRoomTypesUseCase;
use App\Application\UseCases\GetRoomTypeUseCase;
use App\Application\UseCases\UpdateRoomTypeUseCase;
use App\Application\UseCases\CreateRoomUseCase;
use App\Application\UseCases\UpdateRoomUseCase;
use App\Application\UseCases\DeleteRoomUseCase;
use App\Application\UseCases\GetRoomUseCase;
use App\Application\UseCases\GetAllRoomUseCase;
use App\Application\UseCases\FilterRoomByRoomNumberUseCase;
use App\Application\UseCases\FilterRoomByStatusUseCase;
use App\Application\Validators\RoomTypeValidator;
use App\Application\Validators\RoomValidator;
use App\Core\Container\Container;
use App\Domain\Interfaces\Repositories\RoomTypeRepositoryInterface;
use App\Domain\Interfaces\Repositories\RoomRepositoryInterface;

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
        self::registerRoomUseCases($container);
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

    private static function registerRoomUseCases(Container $container): void{
        $container->bind(CreateRoomUseCase::class, function (Container $c) {
            return new CreateRoomUseCase(
                $c->make(RoomRepositoryInterface::class),
                $c->make(RoomValidator::class)
            );
        });

        $container->bind(GetRoomUseCase::class, function (Container $c) {
            return new GetRoomUseCase(
                $c->make(RoomRepositoryInterface::class)
            );
        });

        $container->bind(GetAllRoomUseCase::class, function (Container $c) {
            return new GetAllRoomUseCase(
                $c->make(RoomRepositoryInterface::class)
            );
        });

        $container->bind(UpdateRoomUseCase::class, function (Container $c) {
            return new UpdateRoomUseCase(
                $c->make(RoomRepositoryInterface::class),
                $c->make(RoomValidator::class)
            );
        });

        $container->bind(DeleteRoomUseCase::class, function (Container $c) {
            return new DeleteRoomUseCase(
                $c->make(RoomRepositoryInterface::class)
            );
        });

        $container->bind(FilterRoomByStatusUseCase::class, function (Container $c) {
            return new FilterRoomByStatusUseCase(
                $c->make(RoomRepositoryInterface::class)
            );
        });

        $container->bind(FilterRoomByRoomNumberUseCase::class, function (Container $c) {
            return new FilterRoomByRoomNumberUseCase(
                $c->make(RoomRepositoryInterface::class)
            );
        });
    }
}

