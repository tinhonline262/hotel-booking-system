<?php

namespace App\Infrastructure\DIContainer;

use App\Application\Interfaces\BookingServiceInterface;
use App\Application\Interfaces\RoomServiceInterface;
use App\Application\Services\BookingService;
use App\Application\Services\RoomService;
use App\Application\Services\RoomTypeService;
use App\Application\Services\AuthService;
use App\Application\Interfaces\RoomImageServiceInterface;
use App\Core\Container\Container;
use App\Domain\Interfaces\Repositories\BookingRepositoryInterface;
use App\Presentation\Controllers\Api\BookingController;
use App\Presentation\Controllers\Api\CRUDbookingController;
use App\Presentation\Controllers\Api\RoomController;
use App\Presentation\Controllers\Api\RoomDetailController;
use App\Presentation\Controllers\Api\RoomImageController;
use App\Presentation\Controllers\Api\RoomTypeController;
use App\Presentation\Controllers\Api\SearchController;
use App\Presentation\Controllers\Api\AuthController;

/**
 * Controller Provider - Register all controllers
 */
class ControllerProvider
{
    public static function register(Container $container): void
    {
        // RoomType Controller
        $container->bind(RoomTypeController::class, function (Container $c) {
            return new RoomTypeController(
                $c->make(RoomTypeService::class)
            );
        });

        // RoomImage Controller
        $container->bind(RoomImageController::class, function (Container $c) {
            return new RoomImageController(
                $c->make(RoomImageServiceInterface::class)
            );
        });

        // Room Controller
        $container->bind(RoomController::class, function (Container $c) {
            return new RoomController(
                $c->make(RoomService::class)
            );
        });

        // Booking Controller
        $container->bind(BookingController::class, function (Container $c) {
            return new BookingController(
                $c->make(BookingService::class)
            );
        });

        // CRUD Booking Controller
        $container->bind(CRUDbookingController::class, function (Container $c) {
            return new CRUDbookingController(
                $c->make(BookingService::class)
            );
        });

        // Room Detail Controller
        $container->bind(RoomDetailController::class, function (Container $c) {
            return new RoomDetailController(
                $c->make(RoomService::class)
            );
        });

        // Search Controller
        $container->bind(SearchController::class, function (Container $c) {
            return new SearchController(
                $c->make(RoomService::class),
                $c->make(BookingRepositoryInterface::class)
            );
        });

        // Auth Controller
        $container->bind(AuthController::class, function (Container $c) {
            return new AuthController(
                $c->make(AuthService::class)
            );
        });
    }
}