<?php
namespace App\Application\Interfaces;

use App\Application\DTOs\RevenueDataPointDTO;
use App\Application\DTOs\PaginatedResultDTO;
use App\Application\DTOs\TodayStatsDTO;
use App\Application\DTOs\StatusSummaryDTO;

/**
 * Interface cho các dịch vụ truy vấn thông tin cho Dashboard.
 * Định nghĩa các phương thức nghiệp vụ để lấy dữ liệu thống kê.
 */
interface IDashboardQueryService
{
    /**
     * Lấy các số liệu thống kê trong ngày hôm nay.
     */
    public function getTodayStats(): TodayStatsDTO;

    /**
     * Lấy tóm tắt số lượng phòng theo từng trạng thái.
     *
     * @return StatusSummaryDTO[]
     */
    public function getRoomStatusSummary(): array;

    /**
     * Lấy doanh thu theo khoảng ngày (cho biểu đồ).
     *
     * @return RevenueDataPointDTO[]
     */
    public function getRevenueByDateRange(string $start_date, string $end_date): array;

    /**
     * Lấy doanh thu chi tiết mỗi ngày trong một tháng cụ thể.
     *
     * @return RevenueDataPointDTO[]
     */
    public function getMonthlyRevenue(int $year, int $month): array;

    /**
     * Lấy tóm tắt số lượng booking theo từng trạng thái.
     *
     * @return StatusSummaryDTO[]
     */
    public function getBookingsByStatus(): array;

    /**
     * Lấy danh sách các booking gần đây, có phân trang.
     */
    public function getRecentBookings(int $page = 1, int $limit = 10, ?string $type = null): PaginatedResultDTO;

    /**
     * Lấy tỷ lệ lấp đầy phòng hiện tại (tính theo %).
     */
    public function getOccupancyRate(): float;
}