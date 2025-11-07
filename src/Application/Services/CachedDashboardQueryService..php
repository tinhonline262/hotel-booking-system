<?php
namespace Hotel\Application\Services;

use Hotel\Application\Interfaces\IDashboardQueryService;
use Hotel\Application\Interfaces\ICacheService;
use Hotel\Application\DTOs\RevenueDataPointDTO;
use Hotel\Application\DTOs\PaginatedResultDTO;
use Hotel\Application\DTOs\TodayStatsDTO;
use Hotel\Application\DTOs\StatusSummaryDTO;

class CachedDashboardQueryService implements IDashboardQueryService // <--- ĐÃ SỬA
{
    public function __construct(
        private IDashboardQueryService $decoratee, // Dịch vụ thật (MySql)
        private ICacheService $cache
    ) {}

    public function getTodayStats(): TodayStatsDTO
    {
        $cacheKey = 'dashboard.today_stats';
        $cached = $this->cache->get($cacheKey); // <-- ĐÃ SỬA (Lỗi của tôi)
        if ($cached !== null) return $cached;

        $data = $this->decoratee->getTodayStats();
        $this->cache->set($cacheKey, $data, 300); // 5 phút
        return $data;
    }

    public function getRoomStatusSummary(): array
    {
        $cacheKey = 'dashboard.room_status_summary';
        $cached = $this->cache->get($cacheKey); // <-- ĐÃ SỬA (Lỗi của tôi)
        if ($cached !== null) return $cached;

        $data = $this->decoratee->getRoomStatusSummary();
        $this->cache->set($cacheKey, $data, 300); // 5 phút
        return $data;
    }

    public function getRevenueByDateRange(string $start_date, string $end_date): array
    {
        $cacheKey = "dashboard.revenue_range.{$start_date}_to_{$end_date}";
        $cached = $this->cache->get($cacheKey); // <-- ĐÃ SỬA (Lỗi của tôi)
        if ($cached !== null) return $cached;

        $data = $this->decoratee->getRevenueByDateRange($start_date, $end_date);
        $ttl = (strtotime($end_date) < strtotime('today')) ? 3600 : 300; // Cũ 1h, mới 5p
        $this->cache->set($cacheKey, $data, $ttl);
        return $data;
    }

    public function getMonthlyRevenue(int $year, int $month): array
    {
        $cacheKey = "dashboard.revenue_month.{$year}_{$month}";
        $cached = $this->cache->get($cacheKey); // <-- ĐÃ SỬA (Lỗi của tôi)
        if ($cached !== null) return $cached;
        
        $data = $this->decoratee->getMonthlyRevenue($year, $month);
        $ttl = (new \DateTime("{$year}-{$month}-01"))->format('Y-m') < date('Y-m') ? 86400 : 3600; // Tháng cũ 1 ngày, tháng này 1h
        $this->cache->set($cacheKey, $data, $ttl);
        return $data;
    }

    public function getBookingsByStatus(): array
    {
        $cacheKey = 'dashboard.booking_status';
        $cached = $this->cache->get($cacheKey); // <-- ĐÃ SỬA (Lỗi của tôi)
        if ($cached !== null) return $cached;
        
        $data = $this->decoratee->getBookingsByStatus();
        $this->cache->set($cacheKey, $data, 300); // 5 phút
        return $data;
    }

    public function getRecentBookings(int $page = 1, int $limit = 10, ?string $type = null): PaginatedResultDTO
    {
        $cacheKey = "dashboard.recent_bookings.p{$page}.l{$limit}.t{$type}";
        $cached = $this->cache->get($cacheKey); // <-- ĐÃ SỬA (Lỗi của tôi)
        if ($cached !== null) return $cached;

        $data = $this->decoratee->getRecentBookings($page, $limit, $type);
        $this->cache->set($cacheKey, $data, 60); // 1 phút
        return $data;
    }

    public function getOccupancyRate(): float
    {
        $cacheKey = 'dashboard.occupancy_rate';
        $cached = $this->cache->get($cacheKey); // <-- ĐÃ SỬA (Lỗi của tôi)
        if ($cached !== null) return (float) $cached;
        
        $data = $this->decoratee->getOccupancyRate();
        $this->cache->set($cacheKey, $data, 300); // 5 phút
        return $data;
    }
}