<?php

namespace App\Application\Interfaces;

use App\Domain\Entities\Room;
use App\Application\DTOs\RoomDTO;
interface RoomServiceInterface
{
    public function CreateRoom(RoomDTO $roomDTO) :bool;
    public function UpdateRoom(RoomDTO $roomDTO, int $id): bool;
    public function DeleteRoom(int $id): bool;
    public function GetRoom(int $id): Room;
    public function GetAllRooms(): array;
    public function FilterRoomByStatus(string $status): array;
    public function FilterRoomByRoomNumber(string $roomNumber): ?Room;
    public function Details(int $id): ?array;

}