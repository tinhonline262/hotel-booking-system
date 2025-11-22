<?php

namespace App\Application\UseCases;

use App\Domain\Entities\RoomImage;
use App\Domain\Exceptions\RoomNotFoundException;
use App\Domain\Interfaces\Repositories\RoomImageRepositoryInterface;
use App\Domain\Interfaces\Repositories\RoomRepositoryInterface;
use App\Domain\Interfaces\Repositories\RoomTypeRepositoryInterface;
use App\Domain\ValueObjects\UploadedFile;
use App\Infrastructure\Services\ImageUploadFacade;

class UploadRoomImagesUseCase
{
    private RoomImageRepositoryInterface $repository;
    private ImageUploadFacade $uploadFacade;
    private RoomRepositoryInterface $roomRepository;

    public function __construct(
        RoomImageRepositoryInterface $repository,
        ImageUploadFacade $uploadFacade,
        RoomRepositoryInterface $roomRepository
    ) {
        $this->repository = $repository;
        $this->uploadFacade = $uploadFacade;
        $this->roomRepository = $roomRepository;
    }

    /**
     * Upload multiple images for a room
     * @throws RoomNotFoundException
     */
    public function execute(int $roomId, array $files, ?string $storageType = null): array
    {
        $uploadedImages = [];

        if (!$this->roomRepository->exists($roomId)) {
            throw new RoomNotFoundException($roomId);
        }
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

            if ($imageId = $this->repository->save($image)) {
                $image->setId($imageId);
                $uploadedImages[] = $image->toArray();
            }
        }

        return $uploadedImages;
    }
}
