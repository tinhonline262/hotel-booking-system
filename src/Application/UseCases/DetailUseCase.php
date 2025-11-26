<?php

namespace App\Application\UseCases;
use App\Application\DTOs\RoomDTO;
use App\Application\Validators\RoomValidator;
use App\Domain\Entities\Room;
use App\Domain\Exceptions\InvalidRoomDataExceptions;
use App\Domain\Interfaces\Repositories\RoomRepositoryInterface;
class DetailUseCase
{
    private RoomRepositoryInterface $repository;
    public function __construct(RoomRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $id): ?array{
        return $this->repository->details($id);
    }
}