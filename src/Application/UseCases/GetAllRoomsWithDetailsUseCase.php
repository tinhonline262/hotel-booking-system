<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\Repositories\RoomRepositoryInterface;

class GetAllRoomsWithDetailsUseCase
{
    private RoomRepositoryInterface $roomRepository;

    public function __construct(RoomRepositoryInterface $roomRepository)
    {
        $this->roomRepository = $roomRepository;
    }

    /**
     * Execute use case to get all rooms with detailed information
     *
     * @return array Array of detailed room data
     */
    public function execute(): array
    {
        return $this->roomRepository->findAllWithDetails();
    }
}
