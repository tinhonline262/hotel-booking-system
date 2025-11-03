<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Room;

/**
 * Room Repository Interface
 */
interface RoomRepositoryInterface
{
    public function findById(int $id): ?Room;
    public function findAll(): array;
    public function findAvailable(): array;
    public function findByType(string $type): array;
    public function findAvailableByDateRange(\DateTime $checkIn, \DateTime $checkOut): array;
    public function save(Room $room): bool;
    public function update(Room $room, int $id): bool;
    public function delete(int $id): bool;
    public function exists(int $id): bool;
}

