<?php
namespace App\Infrastructure\Persistence\Repositories;

// ===>>> SỬA DÒNG NÀY <<<===
use App\Domain\Repositories\BookingRepositoryInterface; // Dùng namespace đúng
use App\Core\Database\IDatabaseConnection;
use PDO;

class MySqlBookingRepository implements BookingRepositoryInterface
{
    private $db;

    public function __construct(IDatabaseConnection $db)
    {
        $this->db = $db->getConnection();
    }

    public function findByCode(string $code): ?array
    {
        $sql = "
            SELECT 
                b.id, b.booking_code, b.customer_name, b.customer_email, b.customer_phone,
                b.check_in_date, b.check_out_date, b.total_price, b.status, b.created_at,
                r.room_number,
                rt.name as room_type_name,
                rt.image_url as room_image
            FROM bookings b
            JOIN rooms r ON b.room_id = r.id
            JOIN room_types rt ON r.room_type_id = rt.id
            WHERE b.booking_code = :code
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':code' => $code]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }
}