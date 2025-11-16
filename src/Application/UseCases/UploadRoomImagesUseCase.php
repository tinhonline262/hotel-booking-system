<?php

namespace App\Application\UseCases;

use App\Domain\Entities\RoomImage;
use App\Domain\Interfaces\Repositories\RoomImageRepositoryInterface;
use App\Domain\ValueObjects\UploadedFile;
use App\Infrastructure\Services\ImageUploadFacade;

class UploadRoomImagesUseCase
{
    private RoomImageRepositoryInterface $repository;
    private ImageUploadFacade $uploadFacade;


    public function __construct(
        RoomImageRepositoryInterface $repository,
        ImageUploadFacade $uploadFacade
    ) {
        $this->repository = $repository;
        $this->uploadFacade = $uploadFacade;
    }

    /**
     * Upload multiple images for a room
     */
    public function execute(int $roomId, array $files, ?string $storageType = null): array
    {
        $uploadedImages = [];

        
        // Convert to UploadedFile objects if needed
        $uploadedFiles = array_map(function ($file) {
            return $file instanceof UploadedFile ? $file : UploadedFile::fromArray($file);
        }, $files);

        // Upload files
        $uploadResults = $this->uploadFacade->uploadMultiple(
            $uploadedFiles,
            'rooms',
            $storageType
        );

        // Get current max display order
        $existingImages = $this->repository->findByRoomId($roomId);
        $maxOrder = 0;
        foreach ($existingImages as $img) {
            $maxOrder = max($maxOrder, $img->getDisplayOrder());
        }

        // Check if we should set the first image as primary
        $shouldSetPrimary = empty($existingImages);

        // Save each uploaded image
        foreach ($uploadResults as $index => $result) {
            $isPrimary = $shouldSetPrimary && $index === 0;
            
            $image = new RoomImage(
                null,
                $roomId,
                $result['url'],
                $result['storage_type'] ?? $storageType ?? 'local',
                $result['cloudinary_public_id'] ?? null,
                $result['file_size'] ?? 0,
                $result['mime_type'] ?? 'application/octet-stream',
                ++$maxOrder,
                $isPrimary,
                null
            );

            if ($this->repository->save($image)) {
                $uploadedImages[] = $image->toArray();
            }
        }

        return $uploadedImages;
    }
}
