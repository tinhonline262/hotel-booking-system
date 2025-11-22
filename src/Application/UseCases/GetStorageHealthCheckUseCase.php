<?php

namespace App\Application\UseCases;

use App\Infrastructure\Services\ImageUploadFacade;

class GetStorageHealthCheckUseCase
{
    private ImageUploadFacade $facade;

    public function __construct(ImageUploadFacade $facade)
    {
        $this->facade = $facade;
    }

    /**
     * Get health check for storage provider(s)
     *
     * @param string|null $storageType Specific storage type or null for all
     * @return array
     */
    public function execute(?string $storageType = null): array
    {
        return $this->facade->healthCheck($storageType);
    }
}

