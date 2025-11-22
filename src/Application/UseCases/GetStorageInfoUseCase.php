<?php

namespace App\Application\UseCases;

use App\Infrastructure\Services\ImageUploadFacade;
use App\Domain\Interfaces\Repositories\RoomImageRepositoryInterface;

class GetStorageInfoUseCase
{
    private ImageUploadFacade $uploadFacade;
    private RoomImageRepositoryInterface $repository;

    public function __construct(
        ImageUploadFacade $uploadFacade,
        RoomImageRepositoryInterface $repository
    ) {
        $this->uploadFacade = $uploadFacade;
        $this->repository = $repository;
    }

    public function execute(?string $storageType = null): array
    {
        $info = $this->uploadFacade->getStorageInfo($storageType);

        // Add database statistics
        if ($storageType === null) {
            $info['database'] = [
                'local' => [
                    'totalStorageUsed' => $this->repository->getTotalStorageUsed('local'),
                ],
                'cloudinary' => [
                    'totalStorageUsed' => $this->repository->getTotalStorageUsed('cloudinary'),
                ],
            ];
        } else {
            $info['totalStorageUsedInDb'] = $this->repository->getTotalStorageUsed($storageType);
        }

        return $info;
    }
}
