<?php

namespace App\Domain\Interfaces\Services;

use App\Domain\ValueObjects\UploadedFile;

interface ImageStorageInterface
{
    /**
     * Store an uploaded file
     *
     * @param UploadedFile $file The file to store
     * @param string $directory The directory to store the file in
     * @return array ['path' => string, 'url' => string, 'storage_type' => string, 'file_size' => int, 'mime_type' => string]
     */
    public function store(UploadedFile $file, string $directory): array;

    /**
     * Delete a file
     *
     * @param string $path The file path
     * @param string|null $publicId Optional public ID (for cloud storage)
     * @return bool True if deleted successfully
     */
    public function delete(string $path, ?string $publicId = null): bool;

    /**
     * Check if file exists
     *
     * @param string $path The file path
     * @return bool True if file exists
     */
    public function exists(string $path): bool;

    /**
     * Get public URL for file
     *
     * @param string $path The file path
     * @return string The public URL
     */
    public function getUrl(string $path): string;

    /**
     * Get storage type identifier
     *
     * @return string Storage type (e.g., 'local', 'cloudinary')
     */
    public function getStorageType(): string;

    /**
     * Health check for storage
     *
     * @return array Status information
     */
    public function healthCheck(): array;

    /**
     * Get storage information (capacity, usage, etc.)
     *
     * @return array Storage information
     */
    public function getStorageInfo(): array;
}

