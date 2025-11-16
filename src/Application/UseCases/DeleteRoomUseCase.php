<?php

namespace App\Application\UseCases;
use App\Domain\Exceptions\RoomNotFoundException;
use App\Domain\Interfaces\Repositories\RoomRepositoryInterface;

class DeleteRoomUseCase
{
    private RoomRepositoryInterface $repository;
    public function __construct(RoomRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @throws RoomNotFoundException
     */
    public function execute(int $id): bool
    {
        // Check if exists
        if (!$this->repository->exists($id)) {
            throw new RoomNotFoundException($id);
        }

        // Delete from repository
        return $this->repository->delete($id);
    }
}