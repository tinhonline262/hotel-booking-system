<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Booking;

/**
 * Booking Repository Interface
 */
interface BookingRepositoryInterface
{
    public function findById(int $id): ?Booking;
    public function findAll(): array;
    public function findByUserId(int $userId): array;
    public function findByRoomId(int $roomId): array;
    public function findByStatus(string $status): array;
    public function findUpcoming(): array;
    public function save(Booking $booking): bool;
    public function update(Booking $booking): bool;
    public function delete(int $id): bool;
    public function hasConflict(int $roomId, \DateTime $checkIn, \DateTime $checkOut, ?int $excludeBookingId = null): bool;
}

