<?php

namespace App\Infrastructure\Persistence\Repositories;
use App\Core\Database\Database;
use App\Domain\Entities\Booking;
use App\Domain\Entities\Room;
use App\Domain\Interfaces\Repositories\BookingRepositoryInterface;

class BookingRepository implements BookingRepositoryInterface
{
    private Database $database;
    public function __construct(Database $database){
        $this->database = $database;
    }
    public function save(Booking $booking): bool
    {
        // TODO: Implement save() method.
        $stmt = $this->database->query("INSERT INTO bookings
                (booking_code, room_id, customer_name, customer_email, customer_phone, check_in_date,
                 check_out_date, num_guests, total_price, status, special_requests) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [
            $booking->getBookingCode(),
            $booking->getRoomId(),
            $booking->getCustomerName(),
            $booking->getCustomerEmail(),
            $booking->getCustomerPhone(),
            Date($booking->getCheckInDate()),
            Date($booking->getCheckOutDate()),
            $booking->getNumGuests(),
            $booking->getTotalPrice(),
            $booking->getStatus(),
            $booking->getSpecialRequests()
        ]);
        return true;
    }

    public function update(Booking $booking, int $id): bool
    {
        // TODO: Implement update() method.
        $stmt = $this->database->query("UPDATE bookings SET 
                customer_name = ?, customer_email = ?,  customer_phone = ?, check_in_date = ?, check_out_date = ?, num_guests = ?, total_price = ?,status = ?, special_requests = ?
                WHERE id = ?", [
            $booking->getCustomerName(),
            $booking->getCustomerEmail(),
            $booking->getCustomerPhone(),
            Date($booking->getCheckInDate()),
            Date($booking->getCheckOutDate()),
            $booking->getNumGuests(),
            $booking->getTotalPrice(),
            $booking->getStatus(),
            $booking->getSpecialRequests(),
            $id
        ]);

        return true;
    }

    public function delete(int $id): bool
    {
        // TODO: Implement delete() method.
        $this->database->query("DELETE FROM bookings WHERE id = ?", [$id]);
        return true;
    }

    public function findById(int $id): ?Booking
    {
        // TODO: Implement findById() method.
        $stmt = $this->database->query(
            "SELECT * FROM bookings WHERE id = ?",
            [$id]
        );

        $data = $stmt->fetch();
        return $data ? $this->mapToEntity($data) : null;
    }

    public function findAll(): array
    {
        // TODO: Implement findAll() method.
        $stmt = $this->database->query("SELECT * FROM bookings ORDER BY booking_code");
        $bookings = [];

        while ($data = $stmt->fetch()) {
            $bookings[] = $this->mapToEntity($data);
        }

        return $bookings;
    }

    public function findByCheckInDate(string $date): array
    {
        // TODO: Implement findByCheckInDate() method.
        $stmt = $this->database->query(
            "SELECT * FROM bookings WHERE check_in_date = ? ORDER BY booking_code",
            [$date]
        );
        $bookings = [];

        while ($data = $stmt->fetch()) {
            $bookings[] = $this->mapToEntity($data);
        }

        return $bookings;
    }

    public function findByCheckOutDate(string $date): array
    {
        // TODO: Implement findByCheckOutDate() method.
        $stmt = $this->database->query(
            "SELECT * FROM bookings WHERE check_out_date = ? ORDER BY booking_code",
            [$date]
        );
        $bookings = [];

        while ($data = $stmt->fetch()) {
            $bookings[] = $this->mapToEntity($data);
        }

        return $bookings;
    }

    public function findByStatus(string $status): array
    {
        // TODO: Implement findByStatus() method.
        $stmt = $this->database->query(
            "SELECT * FROM bookings WHERE status = ? ORDER BY booking_code",
            [$status]
        );
        $bookings = [];

        while ($data = $stmt->fetch()) {
            $bookings[] = $this->mapToEntity($data);
        }

        return $bookings;
    }

    public function findByCode(string $code): array
    {
        // TODO: Implement findByCode() method.
        $stmt = $this->database->query(
            "SELECT * FROM bookings WHERE booking_code = ?",
            [$code]
        );

        $bookings = [];

        while ($data = $stmt->fetch()) {
            $bookings[] = $this->mapToEntity($data);
        }

        return $bookings;
    }

    public function exists(int $id): bool
    {
        // TODO: Implement exists() method.
        $stmt = $this->database->query("SELECT COUNT(*) as count FROM bookings WHERE id = ?", [$id]);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }

    private function mapToEntity(array $data): Booking
    {
        return new Booking(
            $data['id'],
            $data['booking_code'],
            $data['room_id'],
            $data['customer_name'],
            $data['customer_email'],
            $data['customer_phone'],
            $data['check_in_date'],
            $data['check_out_date'],
            $data['num_guests'],
            $data['total_price'],
            $data['status'],
            $data['special_requests'],
        );
    }

    public function filterDayByDay(string $start, string $end): array
    {
        // TODO: Implement filterDayByDay() method.
        $stmt = $this->database->query(
            "SELECT * FROM bookings WHERE check_in_date < ? AND check_in_date > ? ORDER BY booking_code",
            [$end, $start]
        );
        $bookings = [];

        while ($data = $stmt->fetch()) {
            $bookings[] = $this->mapToEntity($data);
        }

        return $bookings;
    }
    public function findByPhone(string $phone): array
    {
        // TODO: Implement findByPhone() method.
        $stmt = $this->database->query(
            "SELECT * FROM bookings WHERE customer_phone = ?",
            [$phone]
        );

        $bookings = [];

        while ($data = $stmt->fetch()) {
            $bookings[] = $this->mapToEntity($data);
        }

        return $bookings;
    }

    public function findByEmail(string $email): array
    {
        // TODO: Implement findByEmail() method.
        $stmt = $this->database->query(
            "SELECT * FROM bookings WHERE customer_email = ?",
            [$email]
        );

        $bookings = [];

        while ($data = $stmt->fetch()) {
            $bookings[] = $this->mapToEntity($data);
        }

        return $bookings;
    }

    public function findByName(string $name): array
    {
        // TODO: Implement findByName() method.
        $stmt = $this->database->query(
            "SELECT * FROM bookings WHERE customer_name LIKE ? ORDER BY booking_code",
            ['%' . $name . '%']
        );
        $bookings = [];

        while ($data = $stmt->fetch()) {
            $bookings[] = $this->mapToEntity($data);
        }

        return $bookings;
    }
    public function findRecentWithRoom(int $limit = 10): array
{
    $sql = "
        SELECT 
            b.id, b.booking_code, b.customer_name, 
            b.check_in_date, b.status, b.created_at,
            r.room_number
        FROM bookings b
        LEFT JOIN rooms r ON b.room_id = r.id
        ORDER BY b.created_at DESC
        LIMIT ?
    ";
    
    $stmt = $this->database->query($sql, [$limit]);
    $bookings = [];
    
    while ($row = $stmt->fetch()) {
        $bookings[] = [
            'id' => (int) $row['id'],
            'booking_code' => $row['booking_code'],
            'customer_name' => $row['customer_name'],
            'room_number' => $row['room_number'] ?? 'N/A',
            'check_in_date' => $row['check_in_date'],
            'status' => $row['status'],
            'created_at' => $row['created_at']
        ];
    }
    
    return $bookings;
}
public function findTodayCheckIns(int $limit = 10): array
{
    $today = date('Y-m-d');
    $sql = "
        SELECT 
            b.id, b.booking_code, b.customer_name, 
            b.check_in_date, b.status,
            r.room_number
        FROM bookings b
        LEFT JOIN rooms r ON b.room_id = r.id
        WHERE DATE(b.check_in_date) = ? 
        AND b.status IN ('confirmed', 'pending')
        ORDER BY b.check_in_date
        LIMIT ?
    ";
    
    $stmt = $this->database->query($sql, [$today, $limit]);
    $result = [];
    
    while ($row = $stmt->fetch()) {
        $result[] = [
            'id' => (int) $row['id'],
            'booking_code' => $row['booking_code'],
            'customer_name' => $row['customer_name'],
            'room_number' => $row['room_number'] ?? 'N/A',
            'check_in_date' => $row['check_in_date'],
            'check_in_time' => date('H:i', strtotime($row['check_in_date'])),
            'status' => $row['status']
        ];
    }
    
    return $result;
}

public function findTodayCheckOuts(int $limit = 10): array
{
    $today = date('Y-m-d');
    $sql = "
        SELECT 
            b.id, b.booking_code, b.customer_name, 
            b.check_out_date, b.status,
            r.room_number
        FROM bookings b
        LEFT JOIN rooms r ON b.room_id = r.id
        WHERE DATE(b.check_out_date) = ? 
        AND b.status = 'checked_in'
        ORDER BY b.check_out_date
        LIMIT ?
    ";
    
    $stmt = $this->database->query($sql, [$today, $limit]);
    $result = [];
    
    while ($row = $stmt->fetch()) {
        $result[] = [
            'id' => (int) $row['id'],
            'booking_code' => $row['booking_code'],
            'customer_name' => $row['customer_name'],
            'room_number' => $row['room_number'] ?? 'N/A',
            'check_out_date' => $row['check_out_date'],
            'status' => $row['status']
        ];
    }
    
    return $result;
}
    public function checkRoomAvailable(int $id, string $checkIn, string $checkOut): bool
    {
        $stmt = $this->database->query(" SELECT COUNT(*) as count FROM bookings WHERE room_id = ? AND NOT(check_in_date > ? OR check_out_date < ?)", [$id, $checkOut, $checkIn]);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }

    /**
     * Check if a room has any booking conflicts for given date range
     *
     * @param int $roomId
     * @param string $checkIn Check-in date (Y-m-d)
     * @param string $checkOut Check-out date (Y-m-d)
     * @return bool True if there's a conflict, false if available
     */
    public function hasBookingConflict(int $roomId, string $checkIn, string $checkOut): bool
    {
        // Check for overlapping bookings (excluding cancelled bookings)
        $sql = "
            SELECT COUNT(*) as count 
            FROM bookings 
            WHERE room_id = ? 
            AND status NOT IN ('cancelled', 'completed')
            AND NOT (check_out_date <= ? OR check_in_date >= ?)
        ";

        $stmt = $this->database->query($sql, [$roomId, $checkIn, $checkOut]);
        $result = $stmt->fetch();

        return $result['count'] > 0;
    }

public function countPendingBookings(): int
{
    $sql = "SELECT COUNT(*) as count FROM bookings WHERE status = 'pending'";
    $stmt = $this->database->query($sql);
    $result = $stmt->fetch();
    return (int) $result['count'];
}
}