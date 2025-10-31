<?php

namespace App\Application\UseCases;

use App\Domain\Entities\RoomType;
use App\Domain\Exceptions\RoomTypeNotFoundException;
use App\Domain\Interfaces\Repositories\RoomTypeRepositoryInterface;

class GetRoomTypeUseCase
{
    private RoomTypeRepositoryInterface $repository;

    public function __construct(RoomTypeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $id): RoomType
    {
        $roomType = $this->repository->findById($id);

        if (!$roomType) {
            throw new RoomTypeNotFoundException($id);
        }

        return $roomType;
    }
}

