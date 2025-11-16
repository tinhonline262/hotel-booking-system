<?php

namespace App\Domain\Interfaces\Services;

interface StorageConfigInterface
{
    /**
     * Get a configuration setting by key
     */
    public function getSetting(string $key): ?string;

    /**
     * Set a configuration setting
     */
    public function setSetting(string $key, string $value): bool;

    /**
     * Get maximum allowed file size in bytes
     */
    public function getMaxFileSize(): int;

    /**
     * Get allowed MIME types
     */
    public function getAllowedMimeTypes(): array;

    /**
     * Get local storage limit in bytes
     */
    public function getLocalStorageLimit(): int;

    /**
     * Get default storage type
     */
    public function getDefaultStorageType(): string;

    /**
     * Get Cloudinary configuration
     */
    public function getCloudinaryConfig(): array;
}

