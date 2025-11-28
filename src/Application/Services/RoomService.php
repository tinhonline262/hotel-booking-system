<?php

namespace App\Application\Services;

use App\Application\DTOs\RoomDTO;
use App\Application\Interfaces\RoomServiceInterface;
use App\Application\UseCases\DetailUseCase;
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
use App\Application\UseCases\GetAllRoomsWithDetailsUseCase;
use App\Application\UseCases\GetRoomWithDetailsUseCase;

class RoomService implements RoomServiceInterface
{
    private CreateRoomUseCase $createRoomUseCase;
    private UpdateRoomUseCase $updateRoomUseCase;
    private DeleteRoomUseCase $deleteRoomUseCase;
    private GetAllRoomUseCase $getAllRoomUseCase;
    private GetRoomUseCase $getRoomUseCase;
    private FilterRoomByRoomNumberUseCase $filterRoomByRoomNumberUseCase;
    private FilterRoomByStatusUseCase $filterRoomByStatusUseCase;
    private GetAllRoomsWithDetailsUseCase $getAllRoomsWithDetailsUseCase;
    private GetRoomWithDetailsUseCase $getRoomWithDetailsUseCase;
    private DetailUseCase $detailUseCase;

    public function __construct(
        CreateRoomUseCase $createRoomUseCase,
        UpdateRoomUseCase $updateRoomUseCase,
        DeleteRoomUseCase $deleteRoomUseCase,
        GetAllRoomUseCase $getAllRoomUseCase,
        GetRoomUseCase $getRoomUseCase,
        FilterRoomByRoomNumberUseCase $filterRoomByRoomNumberUseCase,
        FilterRoomByStatusUseCase $filterRoomByStatusUseCase,
        GetAllRoomsWithDetailsUseCase $getAllRoomsWithDetailsUseCase,
        GetRoomWithDetailsUseCase $getRoomWithDetailsUseCase,
        DetailUseCase $detailUseCase
    )
    {
        $this->createRoomUseCase = $createRoomUseCase;
        $this->updateRoomUseCase = $updateRoomUseCase;
        $this->deleteRoomUseCase = $deleteRoomUseCase;
        $this->getAllRoomUseCase = $getAllRoomUseCase;
        $this->getRoomUseCase = $getRoomUseCase;
        $this->filterRoomByRoomNumberUseCase = $filterRoomByRoomNumberUseCase;
        $this->filterRoomByStatusUseCase = $filterRoomByStatusUseCase;
        $this->getAllRoomsWithDetailsUseCase = $getAllRoomsWithDetailsUseCase;
        $this->getRoomWithDetailsUseCase = $getRoomWithDetailsUseCase;
        $this->detailUseCase = $detailUseCase;
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
     * @throws RoomNotFoundException
     */
    public function FilterRoomByRoomNumber(string $roomNumber): ?Room
    {
        return $this->filterRoomByRoomNumberUseCase->execute($roomNumber);
    }

    /**
     * Get all rooms with detailed information (including room type and images)
     */
    public function getAllRoomsWithDetails(): array
    {
        return $this->getAllRoomsWithDetailsUseCase->execute();
    }

    /**
     * Get single room with detailed information (including room type and images)
     * @throws RoomNotFoundException
     */
    public function getRoomWithDetails(int $id): array
    {
        return $this->getRoomWithDetailsUseCase->execute($id);
    }

    public function Details(int $id): ?array
    {
        return $this->detailUseCase->execute($id);
    }
}