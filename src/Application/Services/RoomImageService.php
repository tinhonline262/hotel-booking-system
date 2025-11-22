<?php

namespace App\Application\Services;

use App\Application\Interfaces\RoomImageServiceInterface;
use App\Application\UseCases\UploadRoomImagesUseCase;
use App\Application\UseCases\SetPrimaryImageUseCase;
use App\Application\UseCases\UpdateImageDisplayOrderUseCase;
use App\Application\UseCases\DeleteRoomImageUseCase;
use App\Application\UseCases\GetStorageHealthCheckUseCase;
use App\Application\UseCases\GetStorageInfoUseCase;
use App\Application\UseCases\SwitchStorageProviderUseCase;
use App\Domain\Exceptions\RoomNotFoundException;

/**
 * Room Image Service - Orchestrates all room image operations
 * This is the service layer that controllers call
 */
class RoomImageService implements RoomImageServiceInterface
{
    private UploadRoomImagesUseCase $uploadUseCase;
    private SetPrimaryImageUseCase $setPrimaryUseCase;
    private UpdateImageDisplayOrderUseCase $updateOrderUseCase;
    private DeleteRoomImageUseCase $deleteUseCase;
    private GetStorageHealthCheckUseCase $healthCheckUseCase;
    private GetStorageInfoUseCase $storageInfoUseCase;
    private SwitchStorageProviderUseCase $switchProviderUseCase;

    public function __construct(
        UploadRoomImagesUseCase $uploadUseCase,
        SetPrimaryImageUseCase $setPrimaryUseCase,
        UpdateImageDisplayOrderUseCase $updateOrderUseCase,
        DeleteRoomImageUseCase $deleteUseCase,
        GetStorageHealthCheckUseCase $healthCheckUseCase,
        GetStorageInfoUseCase $storageInfoUseCase,
        SwitchStorageProviderUseCase $switchProviderUseCase
    ) {
        $this->uploadUseCase = $uploadUseCase;
        $this->setPrimaryUseCase = $setPrimaryUseCase;
        $this->updateOrderUseCase = $updateOrderUseCase;
        $this->deleteUseCase = $deleteUseCase;
        $this->healthCheckUseCase = $healthCheckUseCase;
        $this->storageInfoUseCase = $storageInfoUseCase;
        $this->switchProviderUseCase = $switchProviderUseCase;
    }

    /**
     * Upload multiple images for a room
     * @throws RoomNotFoundException
     */
    public function uploadImages(int $roomId, array $files, ?string $storageType = null): array
    {
        return $this->uploadUseCase->execute($roomId, $files, $storageType);
    }

    /**
     * Set an image as primary for a room
     */
    public function setPrimaryImage(int $imageId, int $roomId): bool
    {
        return $this->setPrimaryUseCase->execute($imageId, $roomId);
    }

    /**
     * Update display order for multiple images
     */
    public function updateDisplayOrder(array $orders): bool
    {
        return $this->updateOrderUseCase->execute($orders);
    }

    /**
     * Delete an image
     */
    public function deleteImage(int $imageId): bool
    {
        return $this->deleteUseCase->execute($imageId);
    }

    /**
     * Get storage health check
     */
    public function getStorageHealth(?string $storageType = null): array
    {
        return $this->healthCheckUseCase->execute($storageType);
    }

    /**
     * Get storage information
     */
    public function getStorageInfo(?string $storageType = null): array
    {
        return $this->storageInfoUseCase->execute($storageType);
    }

    /**
     * Switch storage provider
     */
    public function switchStorageProvider(string $provider): bool
    {
        return $this->switchProviderUseCase->execute($provider);
    }
}
