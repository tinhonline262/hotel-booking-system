<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\Repositories\RoomImageRepositoryInterface;
use App\Infrastructure\Services\ImageUploadFacade;

class DeleteRoomImageUseCase
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

    public function execute(int $imageId): bool
    {
        // Find the image
        $image = $this->repository->findById($imageId);

        if (!$image) {
            return false;
        }

        // Delete from storage
        $this->uploadFacade->delete(
            $image->getImageUrl(),
            $image->getCloudinaryPublicId(),
            $image->getStorageType()
        );

        // Delete from database
        return $this->repository->delete($imageId);
    }
}

