<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\Repositories\RoomTypeRepositoryInterface;

class FilterRoomTypesByCapacityUseCase
{
    private RoomTypeRepositoryInterface $repository;

    public function __construct(RoomTypeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $minCapacity): array
    {
        return $this->repository->findByCapacity($minCapacity);
    }
}

