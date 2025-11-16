<?php

namespace App\Application\UseCases;
use App\Domain\Exceptions\RoomNotFoundException;
use App\Domain\Interfaces\Repositories\RoomRepositoryInterface;
use App\Application\DTOs\RoomDTO;
use App\Domain\Entities\Room;
class GetRoomUseCase
{
    private RoomRepositoryInterface $repository;

    public function __construct(RoomRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @throws RoomNotFoundException
     */
    public function execute($id): Room
    {
        $room = $this->repository->findById($id);
        if(!$room){
            throw new RoomNotFoundException($id);
        }
        return $room;
    }
}