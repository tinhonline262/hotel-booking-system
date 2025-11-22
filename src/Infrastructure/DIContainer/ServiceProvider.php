<?php

namespace App\Infrastructure\DIContainer;

use App\Application\Interfaces\RoomImageServiceInterface;
use App\Application\Interfaces\RoomTypeServiceInterface;
use App\Domain\Interfaces\Repositories\RoomRepositoryInterface;
use App\Application\Services\RoomImageService;
use App\Application\Services\RoomTypeService;
use App\Application\Services\RoomService;
use App\Application\UseCases\CreateRoomTypeUseCase;
use App\Application\UseCases\DeleteRoomImageUseCase;
use App\Application\UseCases\DeleteRoomTypeUseCase;
use App\Application\UseCases\FilterRoomTypesByCapacityUseCase;
use App\Application\UseCases\FilterRoomTypesByPriceRangeUseCase;
use App\Application\UseCases\GetAllRoomTypesUseCase;
use App\Application\UseCases\GetRoomTypeUseCase;
use App\Application\UseCases\GetStorageHealthCheckUseCase;
use App\Application\UseCases\GetStorageInfoUseCase;
use App\Application\UseCases\SetPrimaryImageUseCase;
use App\Application\UseCases\SwitchStorageProviderUseCase;
use App\Application\UseCases\UpdateImageDisplayOrderUseCase;
use App\Application\UseCases\UpdateRoomTypeUseCase;
use App\Application\UseCases\CreateRoomUseCase;
use App\Application\UseCases\UpdateRoomUseCase;
use App\Application\UseCases\DeleteRoomUseCase;
use App\Application\UseCases\GetRoomUseCase;
use App\Application\UseCases\GetAllRoomUseCase;
use App\Application\UseCases\FilterRoomByRoomNumberUseCase;
use App\Application\UseCases\FilterRoomByStatusUseCase;
use App\Application\UseCases\UploadRoomImagesUseCase;
use App\Application\Validators\ImageUploadValidator;
use App\Core\Container\Container;
use App\Infrastructure\Persistence\Repositories\RoomRepository;
use App\Domain\Interfaces\Services\StorageConfigInterface;
use App\Infrastructure\Services\ImageStorageFactory;
use App\Infrastructure\Services\ImageUploadFacade;
use App\Application\UseCases\GetAllRoomsWithDetailsUseCase;
use App\Application\UseCases\GetRoomWithDetailsUseCase;

/**
 * Service Provider - Register all application services
 */
class ServiceProvider
{
    public static function register(Container $container): void
    {
        // Infrastructure Services
        self::registerInfrastructureServices($container);

        // RoomType Service
        self::registerRoomTypeService($container);

        // RoomImage Service
        self::registerRoomImageService($container);

        // Add more services here
        // self::registerUserService($container);
        // self::registerBookingService($container);
        self::registerRoomService($container);
    }

    private static function registerInfrastructureServices(Container $container): void
    {
        // Image Storage Factory
        $container->singleton(ImageStorageFactory::class, function (Container $c) {
            return new ImageStorageFactory($c->make(StorageConfigInterface::class));
        });

        // Image Upload Facade
        $container->singleton(ImageUploadFacade::class, function (Container $c) {
            return new ImageUploadFacade(
                $c->make(ImageStorageFactory::class),
                $c->make(ImageUploadValidator::class)
            );
        });
    }

    private static function registerRoomTypeService(Container $container): void
    {
        $container->singleton(RoomTypeService::class, function (Container $c) {
            return new RoomTypeService(
                $c->make(CreateRoomTypeUseCase::class),
                $c->make(GetRoomTypeUseCase::class),
                $c->make(GetAllRoomTypesUseCase::class),
                $c->make(UpdateRoomTypeUseCase::class),
                $c->make(DeleteRoomTypeUseCase::class),
                $c->make(FilterRoomTypesByCapacityUseCase::class),
                $c->make(FilterRoomTypesByPriceRangeUseCase::class)
            );
        });

        // Bind interface to implementation
        $container->bind(RoomTypeServiceInterface::class, function (Container $c) {
            return $c->make(RoomTypeService::class);
        });
    }

    private static function registerRoomService(Container $container): void{
        $container->singleton(RoomService::class, function (Container $c) {
            return new RoomService(
                $c->make(CreateRoomUseCase::class),
                $c->make(UpdateRoomUseCase::class),
                $c->make(DeleteRoomUseCase::class),
                $c->make(GetAllRoomUseCase::class),
                $c->make(GetRoomUseCase::class),
                $c->make(FilterRoomByRoomNumberUseCase::class),
                $c->make(FilterRoomByStatusUseCase::class),
                $c->make(GetAllRoomsWithDetailsUseCase::class),
                $c->make(GetRoomWithDetailsUseCase::class),
            );
        });
        $container->bind(RoomRepositoryInterface::class, function (Container $c) {
            return $c->make(RoomRepository::class);
        });
    }


    private static function registerRoomImageService(Container $container): void
    {
        $container->singleton(RoomImageService::class, function (Container $c) {
            return new RoomImageService(
                $c->make(UploadRoomImagesUseCase::class),
                $c->make(SetPrimaryImageUseCase::class),
                $c->make(UpdateImageDisplayOrderUseCase::class),
                $c->make(DeleteRoomImageUseCase::class),
                $c->make(GetStorageHealthCheckUseCase::class),
                $c->make(GetStorageInfoUseCase::class),
                $c->make(SwitchStorageProviderUseCase::class)
            );
        });

        // Bind interface to implementation
        $container->bind(RoomImageServiceInterface::class, function (Container $c) {
            return $c->make(RoomImageService::class);
        });
    }
}
