<?php

namespace App\Application\UseCases;
use App\Domain\Entities\Room;
use App\Domain\Interfaces\Repositories\RoomRepositoryInterface;
class FilterRoomByStatusUseCase
{
    private RoomRepositoryInterface $roomRepository;

    public function __construct(RoomRepositoryInterface $roomRepository)
    {
        $this->roomRepository = $roomRepository;
    }

    public function execute(string $status): array
    {
        return $this->roomRepository->findByStatus($status);
    }
}