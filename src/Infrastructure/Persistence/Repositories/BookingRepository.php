<?php

namespace App\Infrastructure\Persistence\Repositories;
use App\Core\Database\Database;
use App\Domain\Entities\Booking;
use App\Domain\Entities\Room;
use App\Domain\Interfaces\Repositories\BookingRepositoryInterface;
use Cassandra\Date;

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
            $booking->getCheckInDate(),
            $booking->getCheckOutDate(),
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

    public function findByCode(string $code): ?Booking
    {
        // TODO: Implement findByCode() method.
        $stmt = $this->database->query(
            "SELECT * FROM bookings WHERE booking_code = ?",
            [$code]
        );

        $data = $stmt->fetch();
        return $data ? $this->mapToEntity($data) : null;

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
    public function findByPhone(string $phone): ?Booking
    {
        // TODO: Implement findByPhone() method.
        $stmt = $this->database->query(
            "SELECT * FROM bookings WHERE customer_phone = ?",
            [$phone]
        );

        $data = $stmt->fetch();
        return $data ? $this->mapToEntity($data) : null;

    }

    public function findByEmail(string $email): ?Booking
    {
        // TODO: Implement findByEmail() method.
        $stmt = $this->database->query(
            "SELECT * FROM bookings WHERE customer_email = ?",
            [$email]
        );

        $data = $stmt->fetch();
        return $data ? $this->mapToEntity($data) : null;

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
}