<?php
namespace App\Domain\Interfaces\Repositories;

use App\Domain\Entities\Booking;

interface BookingRepositoryInterface
{
    public function findById(int $id): ?Booking;
    public function findAll(): array;
        
    public function findRecentWithRoom(int $limit = 10): array; // ORDER BY created_at DESC
    public function findTodayCheckIns(int $limit = 10): array;
public function findTodayCheckOuts(int $limit = 10): array;
public function countPendingBookings(): int;
}