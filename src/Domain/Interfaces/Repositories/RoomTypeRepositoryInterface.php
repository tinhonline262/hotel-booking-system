<?php

namespace App\Domain\Interfaces\Repositories;

use App\Domain\Entities\RoomType;

interface RoomTypeRepositoryInterface
{
    /**
     * Find room type by ID
     */
    public function findById(int $id): ?RoomType;

    /**
     * Get all room types
     */
    public function findAll(): array;

    /**
     * Find room types by minimum capacity
     */
    public function findByCapacity(int $capacity): array;

    /**
     * Find room types within price range
     */
    public function findByPriceRange(float $minPrice, float $maxPrice): array;

    /**
     * Find room types by amenity
     */
    public function findByAmenity(string $amenity): array;

    /**
     * Save new room type
     */
    public function save(RoomType $roomType): bool;

    /**
     * Update existing room type
     */
    public function update(RoomType $roomType, int $id): bool;

    /**
     * Delete room type
     */
    public function delete(int $id): bool;

    /**
     * Check if room type exists
     */
    public function exists(int $id): bool;
}

