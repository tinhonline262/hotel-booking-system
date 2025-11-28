<?php
namespace App\Application\Services;

use App\Application\Interfaces\IDashboardQueryService;
use App\Core\Database\IDatabaseConnection;
use App\Application\DTOs\RevenueDataPointDTO;
use App\Application\DTOs\PaginatedResultDTO;
use App\Application\DTOs\TodayStatsDTO;
use App\Application\DTOs\StatusSummaryDTO;

class MySqlDashboardQueryService implements IDashboardQueryService
{
    private $db;

    public function __construct(IDatabaseConnection $db)
    {
        $this->db = $db->getConnection();
    }

    public function getTodayStats(): TodayStatsDTO
    {
        $stmt1 = $this->db->query("
            SELECT COUNT(id) AS todayBookingsCount, SUM(total_price) AS todayRevenue
            FROM bookings
            WHERE DATE(created_at) = CURDATE()
        ");
        $sales = $stmt1->fetch(\PDO::FETCH_ASSOC);
        $stmt2 = $this->db->query("
            SELECT status, COUNT(id) AS count
            FROM rooms
            GROUP BY status
        ");
        $roomCounts = $stmt2->fetchAll(\PDO::FETCH_ASSOC);
        $roomStatus = [];
        foreach ($roomCounts as $row) {
            $roomStatus[$row['status']] = $row['count'];
        }
        return new TodayStatsDTO(
            (int)($sales['todayBookingsCount'] ?? 0),
            (float)($sales['todayRevenue'] ?? 0),
            $roomStatus
        );
    }
    
    public function getRoomStatusSummary(): array
    {
        $stmt = $this->db->query("
            SELECT status, COUNT(id) AS count
            FROM rooms
            GROUP BY status
        ");
        $results = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $results[] = new StatusSummaryDTO($row['status'], (int)$row['count']);
        }
        return $results;
    }

    // ... (Tất cả các phương thức khác: getRevenueByDateRange, getMonthlyRevenue, v.v...)
    
    public function getRevenueByDateRange(string $start_date, string $end_date): array
    {
        $stmt = $this->db->prepare("
            SELECT DATE(check_out_date) AS date, SUM(total_price) AS revenue
            FROM bookings
            WHERE status = 'checked_out'
              AND check_out_date BETWEEN ? AND ?
            GROUP BY DATE(check_out_date)
            ORDER BY date ASC
        ");
        $stmt->execute([$start_date, $end_date]);
        $results = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $results[] = new RevenueDataPointDTO($row['date'], (float)$row['revenue']);
        }
        return $results;
    }
    
    public function getMonthlyRevenue(int $year, int $month): array
    {
        $stmt = $this->db->prepare("
            SELECT DATE(check_out_date) AS date, SUM(total_price) AS revenue
            FROM bookings
            WHERE status = 'checked_out'
              AND YEAR(check_out_date) = ?
              AND MONTH(check_out_date) = ?
            GROUP BY DATE(check_out_date)
            ORDER BY date ASC
        ");
        $stmt->execute([$year, $month]);
        $results = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $results[] = new RevenueDataPointDTO($row['date'], (float)$row['revenue']);
        }
        return $results;
    }

    public function getBookingsByStatus(): array
    {
        $stmt = $this->db->query("
            SELECT status, COUNT(id) AS count
            FROM bookings
            GROUP BY status
        ");
        $results = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $results[] = new StatusSummaryDTO($row['status'], (int)$row['count']);
        }
        return $results;
    }

    public function getRecentBookings(int $page = 1, int $limit = 10, ?string $type = null): PaginatedResultDTO
    {
        $page = max(1, $page);
        $limit = max(1, $limit);
        $offset = ($page - 1) * $limit;
        $filterColumn = 'status'; 
        $countSql = "SELECT COUNT(id) FROM bookings WHERE (? IS NULL OR {$filterColumn} = ?)";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute([$type, $type]);
        $total = (int) $countStmt->fetchColumn();
        $dataSql = "
            SELECT 
                b.id, b.booking_code, b.customer_name, r.room_number,
                b.check_in_date, b.total_price, b.status, b.created_at
            FROM bookings AS b
            JOIN rooms AS r ON b.room_id = r.id
            WHERE (? IS NULL OR b.{$filterColumn} = ?)
            ORDER BY b.created_at DESC
            LIMIT ? OFFSET ?
        ";
        $dataStmt = $this->db->prepare($dataSql);
        $dataStmt->bindValue(1, $type);
        $dataStmt->bindValue(2, $type);
        $dataStmt->bindValue(3, $limit, \PDO::PARAM_INT);
        $dataStmt->bindValue(4, $offset, \PDO::PARAM_INT);
        $dataStmt->execute();
        $data = $dataStmt->fetchAll(\PDO::FETCH_ASSOC);
        return new PaginatedResultDTO($total, $page, $limit, $data);
    }
    
    public function getOccupancyRate(): float
    {
        $stmt = $this->db->query("
            SELECT
                SUM(CASE WHEN status = 'occupied' THEN 1 ELSE 0 END) AS occupied_rooms,
                COUNT(id) AS total_rooms
            FROM rooms
        ");
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (empty($result) || $result['total_rooms'] == 0) {
            return 0.0;
        }
        $rate = ($result['occupied_rooms'] / $result['total_rooms']) * 100;
        return round($rate, 2);
    }
}