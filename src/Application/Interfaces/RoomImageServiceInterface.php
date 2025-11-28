<?php

namespace App\Application\Interfaces;

interface RoomImageServiceInterface
{
    /**
     * Upload multiple images for a room
     */
    public function uploadImages(int $roomId, array $files, ?string $storageType = null): array;

    /**
     * Set an image as primary for a room
     */
    public function setPrimaryImage(int $imageId, int $roomId): bool;

    /**
     * Update display order for multiple images
     */
    public function updateDisplayOrder(array $orders): bool;

    /**
     * Delete an image
     */
    public function deleteImage(int $imageId): bool;

    /**
     * Get storage health check
     */
    public function getStorageHealth(?string $storageType = null): array;

    /**
     * Get storage information
     */
    public function getStorageInfo(?string $storageType = null): array;

    /**
     * Switch storage provider
     */
    public function switchStorageProvider(string $provider): bool;
}

