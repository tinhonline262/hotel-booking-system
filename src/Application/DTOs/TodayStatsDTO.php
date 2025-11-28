<?php
namespace App\Application\DTOs;

class TodayStatsDTO {
    public function __construct(
        public readonly int $todayBookingsCount,
        public readonly float $todayRevenue,
        public readonly array $roomStatus // ví dụ: ['available' => 10, 'occupied' => 5]
    ) {}
}