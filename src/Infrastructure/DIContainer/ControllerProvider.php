<?php

namespace App\Infrastructure\DIContainer;

use App\Application\Interfaces\BookingServiceInterface;
use App\Application\Services\BookingService;
use App\Application\Services\RoomService;
use App\Application\Services\RoomTypeService;
use App\Core\Container\Container;
use App\Presentation\Controllers\Api\BookingController;
use App\Presentation\Controllers\Api\CRUDbookingController;
use App\Presentation\Controllers\Api\RoomController;
use App\Presentation\Controllers\Api\RoomDetailController;
use App\Presentation\Controllers\Api\RoomTypeController;

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

        // Add more controllers here
        // UserController, BookingController, AuthController, etc.
        $container->bind(RoomController::class, function (Container $c) {
            return new RoomController(
                $c->make(RoomService::class)
            );
        });
        $container->bind(BookingController::class, function (Container $c) {
            return new BookingController(
                $c->make(BookingService::class)
            );
        });

        $container->bind(CRUDbookingController::class, function (Container $c) {
            return new CRUDbookingController(
                $c->make(BookingService::class)
            );
        });

        $container->bind(RoomDetailController::class, function (Container $c) {
            return new RoomDetailController(
                $c->make(RoomService::class),
                $c->make(RoomTypeService::class)
            );
        });
    }
}
