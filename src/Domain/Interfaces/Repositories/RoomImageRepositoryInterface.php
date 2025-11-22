<?php

namespace App\Domain\Interfaces\Repositories;

use App\Domain\Entities\RoomImage;

interface RoomImageRepositoryInterface
{
    public function save(RoomImage $image): ?int;
    
    public function findById(int $id): ?RoomImage;
    
    public function findByRoomId(int $roomId): array;
    
    public function findPrimaryByRoomId(int $roomId): ?RoomImage;
    
    public function update(RoomImage $image): bool;
    
    public function delete(int $id): bool;
    
    public function setPrimary(int $id, int $roomId): bool;
    
    public function updateDisplayOrders(array $orders): bool;
    
    public function getTotalStorageUsed(string $storageType = 'local'): int;
    
    public function deleteByRoomId(int $roomId): bool;
}

