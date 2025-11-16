<?php

namespace App\Application\UseCases;
use App\Domain\Entities\Room;
use App\Domain\Exceptions\RoomNotFoundException;
use App\Domain\Interfaces\Repositories\RoomRepositoryInterface;
class FilterRoomByRoomNumberUseCase
{
    private RoomRepositoryInterface $roomRepository;

    public function __construct(RoomRepositoryInterface $roomRepository){
        $this->roomRepository = $roomRepository;
    }

    /**
     * @throws RoomNotFoundException
     */
    public function execute(string $roomNumber): ?Room
    {
        $room = $this->roomRepository->findRoomNumber($roomNumber);
        if(!$room){
            throw new RoomNotFoundException($roomNumber);
        }
        return $room;
    }
}