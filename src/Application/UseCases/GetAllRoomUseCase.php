<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\Repositories\RoomRepositoryInterface;
class GetAllRoomUseCase
{
    private RoomRepositoryInterface $roomRepository;

    public function __construct(RoomRepositoryInterface $roomRepository)
    {
        $this->roomRepository = $roomRepository;
    }

    public function execute() : array
    {
        return $this->roomRepository->findAll();
    }
}