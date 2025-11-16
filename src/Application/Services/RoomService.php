<?php

namespace App\Application\Services;

use App\Application\DTOs\RoomDTO;
use App\Application\Interfaces\RoomServiceInterface;
use App\Domain\Entities\Room;
use App\Domain\Exceptions\InvalidRoomDataExceptions;
use App\Domain\Exceptions\RoomNotFoundException;
use App\Application\UseCases\CreateRoomUseCase;
use App\Application\UseCases\UpdateRoomUseCase;
use App\Application\UseCases\DeleteRoomUseCase;
use App\Application\UseCases\GetRoomUseCase;
use App\Application\UseCases\GetAllRoomUseCase;
use App\Application\UseCases\FilterRoomByRoomNumberUseCase;
use App\Application\UseCases\FilterRoomByStatusUseCase;

class RoomService implements RoomServiceInterface
{
    private CreateRoomUseCase $createRoomUseCase;
    private UpdateRoomUseCase $updateRoomUseCase;
    private DeleteRoomUseCase $deleteRoomUseCase;
    private GetAllRoomUseCase $getAllRoomUseCase;
    private GetRoomUseCase $getRoomUseCase;
    private FilterRoomByRoomNumberUseCase $filterRoomByRoomNumberUseCase;
    private FilterRoomByStatusUseCase $filterRoomByStatusUseCase;

    public function __construct(CreateRoomUseCase $createRoomUseCase, UpdateRoomUseCase $updateRoomUseCase, DeleteRoomUseCase $deleteRoomUseCase,
    GetAllRoomUseCase $getAllRoomUseCase, GetRoomUseCase $getRoomUseCase, FilterRoomByRoomNumberUseCase $filterRoomByRoomNumberUseCase,
                                FilterRoomByStatusUseCase $filterRoomByStatusUseCase)
    {
        $this->createRoomUseCase = $createRoomUseCase;
        $this->updateRoomUseCase = $updateRoomUseCase;
        $this->deleteRoomUseCase = $deleteRoomUseCase;
        $this->getAllRoomUseCase = $getAllRoomUseCase;
        $this->getRoomUseCase = $getRoomUseCase;
        $this->filterRoomByRoomNumberUseCase = $filterRoomByRoomNumberUseCase;
        $this->filterRoomByStatusUseCase = $filterRoomByStatusUseCase;

    }

    /**
     * @throws InvalidRoomDataExceptions
     */
    public function CreateRoom(RoomDTO $roomDTO): bool
    {
        return $this->createRoomUseCase->Execute($roomDTO);
    }

    /**
     * @throws RoomNotFoundException
     * @throws InvalidRoomDataExceptions
     */
    public function UpdateRoom(RoomDTO $roomDTO, int $id): bool
    {
        return $this->updateRoomUseCase->Execute($roomDTO, $id);
    }

    /**
     * @throws RoomNotFoundException
     */
    public function DeleteRoom(int $id): bool
    {
       return $this->deleteRoomUseCase->Execute($id);
    }

    /**
     * @throws RoomNotFoundException
     */
    public function GetRoom(int $id): Room
    {
        return $this->getRoomUseCase->Execute($id);
    }

    public function GetAllRooms(): array
    {
        return $this->getAllRoomUseCase->Execute();
    }

    public function FilterRoomByStatus(string $status): array
    {
        return $this->filterRoomByStatusUseCase->Execute($status);
    }

    /**
     * @param string $roomNumber
     * @return Room|null
     */
    public function FilterRoomByRoomNumber(string $roomNumber): ?Room
    {
        return $this->filterRoomByRoomNumberUseCase->Execute($roomNumber);
    }
}