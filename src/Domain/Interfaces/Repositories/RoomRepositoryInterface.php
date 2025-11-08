<?php

namespace App\Domain\Interfaces\Repositories;

use App\Domain\Entities\Room;

/**
 * Room Repository Interface
 */
interface RoomRepositoryInterface
{
    // find room by id
    public function findById(int $id): ?Room;
    // find all rooms
    public function findAll(): array;
    // filter room by status
    public function findByStatus(string $status): array;
    public function findRoomNumber(string $roomNumber): ?Room;
    // save new room is created
    public function save(Room $newRoom): bool;
    // update room by id
    public function update(Room $updateRoom, int $id): bool;
    // delete room by id
    public function delete(int $id): bool;
    // check room exists by id
    public function exists(int $id): bool;
}

