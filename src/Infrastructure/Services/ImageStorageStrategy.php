<?php

namespace App\Infrastructure\Services;

use App\Domain\Interfaces\Services\ImageStorageInterface;
use App\Domain\ValueObjects\UploadedFile;

/**
 * Strategy pattern for switching between storage providers at runtime
 */
class ImageStorageStrategy implements ImageStorageInterface
{
    private ImageStorageInterface $storage;

    public function __construct(ImageStorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function setStorage(ImageStorageInterface $storage): void
    {
        $this->storage = $storage;
    }

    public function getStorage(): ImageStorageInterface
    {
        return $this->storage;
    }

    public function store(UploadedFile $file, string $directory): array
    {
        return $this->storage->store($file, $directory);
    }

    public function delete(string $path, ?string $publicId = null): bool
    {
        return $this->storage->delete($path, $publicId);
    }

    public function exists(string $path): bool
    {
        return $this->storage->exists($path);
    }

    public function getUrl(string $path): string
    {
        return $this->storage->getUrl($path);
    }

    public function getStorageType(): string
    {
        return $this->storage->getStorageType();
    }

    public function healthCheck(): array
    {
        return $this->storage->healthCheck();
    }

    public function getStorageInfo(): array
    {
        return $this->storage->getStorageInfo();
    }
}

