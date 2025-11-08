<?php

namespace App\Infrastructure\DIContainer;

use App\Application\Services\RoomTypeService;
use App\Application\Validators\RoomTypeValidator;
use App\Application\Validators\RoomValidator;
use App\Application\Services\RoomService;
use App\Core\Container\Container;

/**
 * Validator Provider - Register all validators
 */
class ValidatorProvider
{
    public static function register(Container $container): void
    {
        // RoomType Validator
        $container->singleton(RoomTypeValidator::class, function () {
            return new RoomTypeValidator();
        });
        // Add more validators here
        // UserValidator, BookingValidator, etc.
        $container->singleton(RoomService::class, function () {
            return new RoomValidator();
        });
    }
}
