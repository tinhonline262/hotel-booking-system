<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\Services\StorageConfigInterface;

class SwitchStorageProviderUseCase
{
    private StorageConfigInterface $config;

    public function __construct(StorageConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * Switch the default storage provider
     *
     * @param string $provider Storage provider type (local, cloudinary)
     * @return bool
     */
    public function execute(string $provider): bool
    {
        // Validate provider
        if (!in_array($provider, ["local", "cloudinary"])) {
            return false;
        }

        // Update default storage type in configuration
        return $this->config->setSetting('default_storage_type', $provider);
    }
}

