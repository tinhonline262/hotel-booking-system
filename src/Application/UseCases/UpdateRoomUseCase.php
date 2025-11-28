<?php

namespace App\Application\UseCases;
use App\Application\Validators\RoomValidator;
use App\Domain\Entities\Room;
use App\Domain\Interfaces\Repositories\RoomRepositoryInterface;
use App\Domain\Exceptions\InvalidRoomDataExceptions;
use App\Domain\Exceptions\RoomNotFoundException;
use App\Application\DTOs\RoomDTO;
class UpdateRoomUseCase
{
    private RoomRepositoryInterface $repository;
    private RoomValidator $validator;

    public function __construct(RoomRepositoryInterface $repository, RoomValidator $validator)
    {
        $this->repository = $repository;
        $this->validator = $validator;
    }

    /**
     * @throws RoomNotFoundException
     * @throws InvalidRoomDataExceptions
     */
    public function execute(RoomDTO $updateRoom, int $id): bool
    {
        if(!$this->repository->exists($id))
        {
            throw new RoomNotFoundException($id);
        }

        $error = $this->validator->validateUpdate($updateRoom->toArray(),$id);
        if(!empty($error))
        {
            throw new InvalidRoomDataExceptions($error);
        }

        $room = new Room(
            $id,
            $updateRoom->room_number,
            $updateRoom->room_type_id,
            $updateRoom->status
        );
        return $this->repository->update($room,$id);
    }
}