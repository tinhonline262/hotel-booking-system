<?php

namespace App\Application\UseCases;

use App\Application\DTOs\DashboardStatsDTO;
use App\Domain\Interfaces\Repositories\DashboardRepositoryInterface;

/**
 * Get Dashboard Statistics Use Case
 */
class GetDashboardStats
{
    private DashboardRepositoryInterface $dashboardRepository;

    public function __construct(DashboardRepositoryInterface $dashboardRepository)
    {
        $this->dashboardRepository = $dashboardRepository;
    }

    /**
     * Execute the use case
     *
     * @param array $params Optional parameters (days for upcoming, limit, etc.)
     * @return DashboardStatsDTO
     */
    public function execute(array $params = []): DashboardStatsDTO
    {
        // Extract parameters with defaults
        $upcomingDays = $params['upcoming_days'] ?? 7;
        $recentLimit = $params['recent_limit'] ?? 10;
        $upcomingLimit = $params['upcoming_limit'] ?? 10;

        // Get room statistics
        $totalRooms = $this->dashboardRepository->getTotalRooms();
        $availableRooms = $this->dashboardRepository->getRoomCountByStatus('available');
        $occupiedRooms = $this->dashboardRepository->getRoomCountByStatus('occupied');
        $cleaningRooms = $this->dashboardRepository->getRoomCountByStatus('cleaning');

        // Get booking statistics
        $totalBookings = $this->dashboardRepository->getTotalBookings();
        $pendingBookings = $this->dashboardRepository->getBookingCountByStatus('pending');
        $confirmedBookings = $this->dashboardRepository->getBookingCountByStatus('confirmed');
        $checkedInBookings = $this->dashboardRepository->getBookingCountByStatus('checked_in');
        $cancelledBookings = $this->dashboardRepository->getBookingCountByStatus('cancelled');

        // Get revenue statistics
        $totalRevenue = $this->dashboardRepository->getTotalRevenue();
        $monthlyRevenue = $this->dashboardRepository->getMonthlyRevenue();
        $todayRevenue = $this->dashboardRepository->getTodayRevenue();

        // Get detailed data
        $recentBookings = $this->dashboardRepository->getRecentBookings($recentLimit);
        $upcomingCheckIns = $this->dashboardRepository->getUpcomingCheckIns($upcomingDays, $upcomingLimit);
        $upcomingCheckOuts = $this->dashboardRepository->getUpcomingCheckOuts($upcomingDays, $upcomingLimit);
        $roomTypeStats = $this->dashboardRepository->getRoomTypeStatistics();

        return new DashboardStatsDTO(
    totalRooms: $totalRooms,
    availableRooms: $availableRooms,
    occupiedRooms: $occupiedRooms,
    cleaningRooms: $cleaningRooms,
    maintenanceRooms: 0, 
    totalRoomTypes: 0, 
    occupancyRate: $occupiedRooms / $totalRooms * 100,
    roomTypeDistribution: $roomTypeStats,
    recentBookings: $recentBookings,
    todayCheckIns: $upcomingCheckIns,
    todayCheckOuts: $upcomingCheckOuts,
    todayCheckInsCount: count($upcomingCheckIns),
    pendingBookingsCount: $pendingBookings
);
    }

    /**
     * Get dashboard repository instance
     * For direct access in controller methods
     *
     * @return DashboardRepositoryInterface
     */
    public function getDashboardRepository(): DashboardRepositoryInterface
    {
        return $this->dashboardRepository;
    }
}