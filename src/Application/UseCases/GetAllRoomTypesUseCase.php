<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\Repositories\RoomTypeRepositoryInterface;

class GetAllRoomTypesUseCase
{
    private RoomTypeRepositoryInterface $repository;

    public function __construct(RoomTypeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(): array
    {
        return $this->repository->findAll();
    }
}

