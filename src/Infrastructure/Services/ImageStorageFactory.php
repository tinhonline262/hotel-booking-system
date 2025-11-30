<?php

namespace App\Infrastructure\Services;

use App\Domain\Exceptions\StorageException;
use App\Domain\Interfaces\Services\ImageStorageInterface;
use App\Domain\Interfaces\Services\StorageConfigInterface;
use App\Infrastructure\ThirdPartyIntegrations\CloudinaryImageStorage;

/**
 * Factory pattern for creating storage instances
 */
class ImageStorageFactory
{
    private StorageConfigInterface $config;
    private array $instances = [];

    public function __construct(StorageConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * Create storage instance by type
     * @throws StorageException
     */
    public function create(string $type): ImageStorageInterface
    {
        // Return cached instance if exists
        if (isset($this->instances[$type])) {
            return $this->instances[$type];
        }

        // Create new instance based on type
        $instance = match($type) {
            'local' => $this->createLocalStorage(),
            'cloudinary' => $this->createCloudinaryStorage(),
            default => throw new StorageException("Unknown storage type: {$type}")
        };

        // Cache the instance
        $this->instances[$type] = $instance;

        return $instance;
    }

    /**
     * Create default storage instance
     * @throws StorageException
     */
    public function createDefault(): ImageStorageInterface
    {
        $defaultType = $this->config->getDefaultStorageType();
        return $this->create($defaultType);
    }

    /**
     * Create local storage instance
     * @throws StorageException
     */
    private function createLocalStorage(): ImageStorageInterface
    {
        $basePath = $this->config->getSetting('local_storage_path') ?? 'public/uploads/rooms';
        $baseUrl = 'uploads';
        $storageLimit = $this->config->getLocalStorageLimit();

        return new LocalImageStorage($basePath, $baseUrl, $storageLimit);
    }

    /**
     * Create Cloudinary storage instance
     */
    private function createCloudinaryStorage(): ImageStorageInterface
    {
        $config = $this->config->getCloudinaryConfig();
        return new CloudinaryImageStorage($config);
    }
}