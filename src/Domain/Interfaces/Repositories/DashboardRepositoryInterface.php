<?php

namespace App\Domain\Interfaces\Repositories;

/**
 * Dashboard Repository Interface
 */
interface DashboardRepositoryInterface
{
    /**
     * Get total count of rooms
     */
    public function getTotalRooms(): int;

    /**
     * Get count of rooms by status
     */
    public function getRoomCountByStatus(string $status): int;

    /**
     * Get total count of bookings
     */
    public function getTotalBookings(): int;

    /**
     * Get count of bookings by status
     */
    public function getBookingCountByStatus(string $status): int;

    /**
     * Get total revenue from all bookings
     */
    public function getTotalRevenue(): float;

    /**
     * Get revenue for current month
     */
    public function getMonthlyRevenue(?int $year = null, ?int $month = null): float;

    /**
     * Get revenue for today
     */
    public function getTodayRevenue(): float;

    /**
     * Get recent bookings (limit default 10)
     */
    public function getRecentBookings(int $limit = 10): array;

    /**
     * Get upcoming check-ins for next N days
     */
    public function getUpcomingCheckIns(int $days = 7, int $limit = 10): array;

    /**
     * Get upcoming check-outs for next N days
     */
    public function getUpcomingCheckOuts(int $days = 7, int $limit = 10): array;

    /**
     * Get statistics by room type
     * Returns array with room type info and booking counts
     */
    public function getRoomTypeStatistics(): array;

    /**
     * Get revenue by date range
     */
    public function getRevenueByDateRange(string $startDate, string $endDate): float;

    /**
     * Get booking trends (daily/weekly/monthly)
     */
    public function getBookingTrends(string $period = 'daily', int $limit = 30): array;
}