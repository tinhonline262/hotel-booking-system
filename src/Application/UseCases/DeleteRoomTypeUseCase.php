<?php

namespace App\Application\UseCases;

use App\Domain\Exceptions\RoomTypeNotFoundException;
use App\Domain\Interfaces\Repositories\RoomTypeRepositoryInterface;

class DeleteRoomTypeUseCase
{
    private RoomTypeRepositoryInterface $repository;

    public function __construct(RoomTypeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $id): bool
    {
        // Check if exists
        if (!$this->repository->exists($id)) {
            throw new RoomTypeNotFoundException($id);
        }

        // Delete from repository
        return $this->repository->delete($id);
    }
}

