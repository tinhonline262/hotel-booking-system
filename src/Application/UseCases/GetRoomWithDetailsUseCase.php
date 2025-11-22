<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\Repositories\RoomRepositoryInterface;
use App\Domain\Exceptions\RoomNotFoundException;

class GetRoomWithDetailsUseCase
{
    private RoomRepositoryInterface $roomRepository;

    public function __construct(RoomRepositoryInterface $roomRepository)
    {
        $this->roomRepository = $roomRepository;
    }

    /**
     * Execute use case to get a single room with detailed information
     *
     * @param int $id Room ID
     * @return array Detailed room data
     * @throws RoomNotFoundException
     */
    public function execute(int $id): array
    {
        $room = $this->roomRepository->findByIdWithDetails($id);

        if (!$room) {
            throw new RoomNotFoundException($id);
        }

        return $room;
    }
}
