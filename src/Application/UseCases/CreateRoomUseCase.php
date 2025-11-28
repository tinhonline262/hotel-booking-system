<?php

namespace App\Application\UseCases;

use App\Application\DTOs\RoomDTO;
use App\Application\Validators\RoomValidator;
use App\Domain\Entities\Room;
use App\Domain\Exceptions\InvalidRoomDataExceptions;
use App\Domain\Interfaces\Repositories\RoomRepositoryInterface;
class CreateRoomUseCase
{
    private RoomRepositoryInterface $repository;
    private RoomValidator $validator;

    public function __construct(RoomRepositoryInterface $repository, RoomValidator $validator){
        $this->repository = $repository;
        $this->validator = $validator;
    }

    /**
     * @throws InvalidRoomDataExceptions
     */
    public function execute(RoomDTO $roomDTO) :bool{
        $error = $this->validator->validate($roomDTO->toArray());
        if(!empty($error))
        {
            throw new InvalidRoomDataExceptions($error);
        }
        $room = new Room(null,$roomDTO->room_number,$roomDTO->room_type_id,$roomDTO->status);

        return $this->repository->save($room);
    }
}