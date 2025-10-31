<?php

namespace App\Application\Interfaces;

use App\Application\DTOs\RoomTypeDTO;
use App\Domain\Entities\RoomType;

interface RoomTypeServiceInterface
{
    public function createRoomType(RoomTypeDTO $dto): bool;

    public function getRoomType(int $id): RoomType;

    public function getAllRoomTypes(): array;

    public function updateRoomType(int $id, RoomTypeDTO $dto): bool;

    public function deleteRoomType(int $id): bool;

    public function filterByCapacity(int $minCapacity): array;

    public function filterByPriceRange(float $minPrice, float $maxPrice): array;
}

