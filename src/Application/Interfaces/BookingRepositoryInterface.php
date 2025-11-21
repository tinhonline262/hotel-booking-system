<?php
namespace App\Domain\Interfaces\Repositories;

use App\Domain\Entities\Booking;

interface BookingRepositoryInterface
{
    public function findById(int $id): ?Booking;
    public function findAll(): array;
    public function getTodayCheckIns(): array;      // checkInDate = today
    public function getTodayCheckOuts(): array;     // checkOutDate = today
    public function getPendingCount(): int;         // status = 'pending'
    public function getRecentBookings(int $limit = 10): array; // ORDER BY created_at DESC
}