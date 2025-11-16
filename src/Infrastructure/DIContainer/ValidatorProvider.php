<?php

namespace App\Infrastructure\DIContainer;

use App\Application\Validators\ImageUploadValidator;
use App\Application\Validators\RoomTypeValidator;
use App\Core\Container\Container;
use App\Domain\Interfaces\Services\StorageConfigInterface;

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

        // Image Upload Validator
        $container->singleton(ImageUploadValidator::class, function (Container $c) {
            $config = $c->make(StorageConfigInterface::class);
            return new ImageUploadValidator(
                $config->getMaxFileSize(),
                $config->getAllowedMimeTypes()
            );
        });

        // Add more validators here
        // UserValidator, BookingValidator, etc.
    }
}
