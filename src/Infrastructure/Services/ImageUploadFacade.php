<?php

namespace App\Infrastructure\Services;

use App\Domain\Exceptions\StorageException;
use App\Domain\Interfaces\Services\ImageStorageInterface;
use App\Domain\ValueObjects\UploadedFile;
use App\Application\Validators\ImageUploadValidator;
use App\Domain\Exceptions\ImageUploadException;

/**
 * Facade pattern for image upload operations
 */
class ImageUploadFacade
{
    private ImageStorageFactory $factory;
    private ImageUploadValidator $validator;
    private ?ImageStorageStrategy $strategy = null;

    public function __construct(
        ImageStorageFactory $factory,
        ImageUploadValidator $validator
    ) {
        $this->factory = $factory;
        $this->validator = $validator;
    }

    /**
     * Upload a single image
     */
    public function uploadSingle(
        UploadedFile $file,
        string $directory = 'rooms',
        ?string $storageType = null
    ): array {
        // Validate file
        if (!$this->validator->validate($file)) {
            throw new ImageUploadException(
                $this->validator->getErrors(),
                'File validation failed'
            );
        }

        // Get storage strategy
        $storage = $this->getStorage($storageType);

        // Upload file
        return $storage->store($file, $directory);
    }

    /**
     * Upload multiple images
     * @throws ImageUploadException
     */
    public function uploadMultiple(
        array $files,
        string $directory = 'rooms',
        ?string $storageType = null
    ): array {
        $results = [];
        $errors = [];

        foreach ($files as $index => $file) {
            if (!($file instanceof UploadedFile)) {
                $errors[] = "File #" . ($index + 1) . ": Invalid file object";
                continue;
            }

            try {
                $results[] = $this->uploadSingle($file, $directory, $storageType);
            } catch (ImageUploadException $e) {
                foreach ($e->getErrors() as $error) {
                    $errors[] = "File #" . ($index + 1) . ": " . $error;
                }
            }
        }

        if (!empty($errors)) {
            throw new ImageUploadException($errors, 'Some files failed to upload');
        }

        return $results;
    }

    /**
     * Delete an image
     * @throws StorageException
     */
    public function delete(string $path, ?string $publicId = null, ?string $storageType = null): bool
    {
        $storage = $this->getStorage($storageType);
        return $storage->delete($path, $publicId);
    }

    /**
     * Check if image exists
     * @throws StorageException
     */
    public function exists(string $path, ?string $storageType = null): bool
    {
        $storage = $this->getStorage($storageType);
        return $storage->exists($path);
    }

    /**
     * Get image URL
     * @throws StorageException
     */
    public function getUrl(string $path, ?string $storageType = null): string
    {
        $storage = $this->getStorage($storageType);
        return $storage->getUrl($path);
    }

    /**
     * Health check for storage
     * @throws StorageException
     */
    public function healthCheck(?string $storageType = null): array
    {
        if ($storageType === null) {
            // Check all storage types
            return [
                'local' => $this->factory->create('local')->healthCheck(),
                'cloudinary' => $this->factory->create('cloudinary')->healthCheck(),
            ];
        }

        $storage = $this->getStorage($storageType);
        return $storage->healthCheck();
    }

    /**
     * Get storage information
     * @throws StorageException
     */
    public function getStorageInfo(?string $storageType = null): array
    {
        if ($storageType === null) {
            // Get info for all storage types
            return [
                'local' => $this->factory->create('local')->getStorageInfo(),
                'cloudinary' => $this->factory->create('cloudinary')->getStorageInfo(),
            ];
        }

        $storage = $this->getStorage($storageType);
        return $storage->getStorageInfo();
    }

    /**
     * Switch storage provider
     * @throws StorageException
     */
    public function switchProvider(string $storageType): void
    {
        $storage = $this->factory->create($storageType);
        if ($this->strategy === null) {
            $this->strategy = new ImageStorageStrategy($storage);
        } else {
            $this->strategy->setStorage($storage);
        }
    }

    /**
     * Get current storage type
     * @throws StorageException
     */
    public function getCurrentStorageType(): string
    {
        if ($this->strategy !== null) {
            return $this->strategy->getStorageType();
        }

        return $this->factory->createDefault()->getStorageType();
    }

    /**
     * @throws StorageException
     */
    private function getStorage(?string $storageType = null): ImageStorageInterface
    {
        if ($storageType !== null) {
            return $this->factory->create($storageType);
        }

        if ($this->strategy !== null) {
            return $this->strategy->getStorage();
        }

        return $this->factory->createDefault();
    }
}
