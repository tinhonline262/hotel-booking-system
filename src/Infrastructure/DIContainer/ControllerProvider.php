<?php

namespace App\Infrastructure\DIContainer;

use App\Application\Services\RoomService;
use App\Application\Interfaces\RoomImageServiceInterface;
use App\Application\Services\RoomTypeService;
use App\Core\Container\Container;
use App\Presentation\Controllers\Api\RoomController;
use App\Presentation\Controllers\Api\RoomImageController;
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

        // RoomImage Controller
        $container->bind(RoomImageController::class, function (Container $c) {
            return new RoomImageController(
                $c->make(RoomImageServiceInterface::class)
            );
        });

        // Add more controllers here
        // UserController, BookingController, AuthController, etc.
        $container->bind(RoomController::class, function (Container $c) {
            return new RoomController(
                $c->make(RoomService::class)
            );
        });
    }
}
