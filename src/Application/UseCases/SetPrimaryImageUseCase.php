<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\Repositories\RoomImageRepositoryInterface;

class SetPrimaryImageUseCase
{
    private RoomImageRepositoryInterface $repository;

    public function __construct(RoomImageRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $imageId, int $roomId): bool
    {
        // Verify the image exists and belongs to the room
        $image = $this->repository->findById($imageId);
        
        if (!$image || $image->getRoomId() !== $roomId) {
            return false;
        }

        return $this->repository->setPrimary($imageId, $roomId);
    }
}

