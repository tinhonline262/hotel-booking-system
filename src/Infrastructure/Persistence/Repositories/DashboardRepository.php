<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Interfaces\Repositories\DashboardRepositoryInterface;
use PDO;

/**
 * Dashboard Repository Implementation
 */
class DashboardRepository implements DashboardRepositoryInterface
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getTotalRooms(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM rooms");
        return (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getRoomCountByStatus(string $status): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM rooms WHERE status = ?");
        $stmt->execute([$status]);
        return (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getTotalBookings(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM bookings");
        return (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getBookingCountByStatus(string $status): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM bookings WHERE status = ?");
        $stmt->execute([$status]);
        return (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getTotalRevenue(): float
    {
        $stmt = $this->db->query(
            "SELECT COALESCE(SUM(total_price), 0) as total 
             FROM bookings 
             WHERE status IN ('confirmed', 'checked_in', 'checked_out')"
        );
        return (float) $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getMonthlyRevenue(?int $year = null, ?int $month = null): float
    {
        $year = $year ?? date('Y');
        $month = $month ?? date('m');
        
        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(total_price), 0) as total 
             FROM bookings 
             WHERE status IN ('confirmed', 'checked_in', 'checked_out')
             AND YEAR(created_at) = ? 
             AND MONTH(created_at) = ?"
        );
        $stmt->execute([$year, $month]);
        return (float) $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getTodayRevenue(): float
    {
        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(total_price), 0) as total 
             FROM bookings 
             WHERE status IN ('confirmed', 'checked_in', 'checked_out')
             AND DATE(created_at) = CURDATE()"
        );
        $stmt->execute();
        return (float) $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getRecentBookings(int $limit = 10): array
    {
        $stmt = $this->db->prepare(
            "SELECT 
                b.id,
                b.booking_code,
                b.customer_name,
                b.customer_email,
                b.customer_phone,
                b.check_in_date,
                b.check_out_date,
                b.num_guests,
                b.total_price,
                b.status,
                b.created_at,
                r.room_number,
                rt.name as room_type_name
             FROM bookings b
             INNER JOIN rooms r ON b.room_id = r.id
             INNER JOIN room_types rt ON r.room_type_id = rt.id
             ORDER BY b.created_at DESC
             LIMIT ?"
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUpcomingCheckIns(int $days = 7, int $limit = 10): array
    {
        $stmt = $this->db->prepare(
            "SELECT 
                b.id,
                b.booking_code,
                b.customer_name,
                b.customer_email,
                b.customer_phone,
                b.check_in_date,
                b.check_out_date,
                b.num_guests,
                b.total_price,
                b.status,
                r.room_number,
                rt.name as room_type_name
             FROM bookings b
             INNER JOIN rooms r ON b.room_id = r.id
             INNER JOIN room_types rt ON r.room_type_id = rt.id
             WHERE b.check_in_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
             AND b.status IN ('confirmed', 'pending')
             ORDER BY b.check_in_date ASC
             LIMIT ?"
        );
        $stmt->execute([$days, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUpcomingCheckOuts(int $days = 7, int $limit = 10): array
    {
        $stmt = $this->db->prepare(
            "SELECT 
                b.id,
                b.booking_code,
                b.customer_name,
                b.customer_email,
                b.customer_phone,
                b.check_in_date,
                b.check_out_date,
                b.num_guests,
                b.total_price,
                b.status,
                r.room_number,
                rt.name as room_type_name
             FROM bookings b
             INNER JOIN rooms r ON b.room_id = r.id
             INNER JOIN room_types rt ON r.room_type_id = rt.id
             WHERE b.check_out_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
             AND b.status = 'checked_in'
             ORDER BY b.check_out_date ASC
             LIMIT ?"
        );
        $stmt->execute([$days, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRoomTypeStatistics(): array
    {
        $stmt = $this->db->query(
            "SELECT 
                rt.id,
                rt.name,
                rt.price_per_night,
                COUNT(r.id) as total_rooms,
                SUM(CASE WHEN r.status = 'available' THEN 1 ELSE 0 END) as available_rooms,
                SUM(CASE WHEN r.status = 'occupied' THEN 1 ELSE 0 END) as occupied_rooms,
                SUM(CASE WHEN r.status = 'cleaning' THEN 1 ELSE 0 END) as cleaning_rooms,
                COALESCE(booking_stats.total_bookings, 0) as total_bookings,
                COALESCE(booking_stats.total_revenue, 0) as total_revenue
             FROM room_types rt
             LEFT JOIN rooms r ON rt.id = r.room_type_id
             LEFT JOIN (
                 SELECT 
                     r.room_type_id,
                     COUNT(b.id) as total_bookings,
                     SUM(b.total_price) as total_revenue
                 FROM bookings b
                 INNER JOIN rooms r ON b.room_id = r.id
                 WHERE b.status IN ('confirmed', 'checked_in', 'checked_out')
                 GROUP BY r.room_type_id
             ) booking_stats ON rt.id = booking_stats.room_type_id
             GROUP BY rt.id, rt.name, rt.price_per_night
             ORDER BY rt.name"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRevenueByDateRange(string $startDate, string $endDate): float
    {
        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(total_price), 0) as total 
             FROM bookings 
             WHERE status IN ('confirmed', 'checked_in', 'checked_out')
             AND DATE(created_at) BETWEEN ? AND ?"
        );
        $stmt->execute([$startDate, $endDate]);
        return (float) $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getBookingTrends(string $period = 'daily', int $limit = 30): array
    {
        $groupBy = match($period) {
            'weekly' => "DATE_FORMAT(created_at, '%Y-%u')",
            'monthly' => "DATE_FORMAT(created_at, '%Y-%m')",
            default => "DATE(created_at)"
        };

        $stmt = $this->db->prepare(
            "SELECT 
                {$groupBy} as period,
                COUNT(*) as total_bookings,
                SUM(CASE WHEN status IN ('confirmed', 'checked_in', 'checked_out') THEN total_price ELSE 0 END) as revenue,
                SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
             FROM bookings
             GROUP BY period
             ORDER BY period DESC
             LIMIT ?"
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} // 👈 thêm dấu ngoặc đóng class ở đây
