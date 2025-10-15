<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Booking;
use App\Domain\Repositories\BookingRepositoryInterface;
use App\Core\Database\Database;
use DateTime;

/**
 * Booking Repository Implementation
 */
class BookingRepository implements BookingRepositoryInterface
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function findById(int $id): ?Booking
    {
        $stmt = $this->database->query("SELECT * FROM bookings WHERE id = ?", [$id]);
        $data = $stmt->fetch();
        return $data ? $this->mapToEntity($data) : null;
    }

    public function findAll(): array
    {
        $stmt = $this->database->query("SELECT * FROM bookings ORDER BY created_at DESC");
        $bookings = [];

        while ($data = $stmt->fetch()) {
            $bookings[] = $this->mapToEntity($data);
        }

        return $bookings;
    }

    public function findByUserId(int $userId): array
    {
        $stmt = $this->database->query(
            "SELECT * FROM bookings WHERE user_id = ? ORDER BY check_in_date DESC",
            [$userId]
        );
        $bookings = [];

        while ($data = $stmt->fetch()) {
            $bookings[] = $this->mapToEntity($data);
        }

        return $bookings;
    }

    public function findByRoomId(int $roomId): array
    {
        $stmt = $this->database->query(
            "SELECT * FROM bookings WHERE room_id = ? ORDER BY check_in_date DESC",
            [$roomId]
        );
        $bookings = [];

        while ($data = $stmt->fetch()) {
            $bookings[] = $this->mapToEntity($data);
        }

        return $bookings;
    }

    public function findByStatus(string $status): array
    {
        $stmt = $this->database->query(
            "SELECT * FROM bookings WHERE status = ? ORDER BY check_in_date DESC",
            [$status]
        );
        $bookings = [];

        while ($data = $stmt->fetch()) {
            $bookings[] = $this->mapToEntity($data);
        }

        return $bookings;
    }

    public function findUpcoming(): array
    {
        $stmt = $this->database->query(
            "SELECT * FROM bookings WHERE check_in_date >= CURDATE() AND status IN ('pending', 'confirmed') ORDER BY check_in_date ASC"
        );
        $bookings = [];

        while ($data = $stmt->fetch()) {
            $bookings[] = $this->mapToEntity($data);
        }

        return $bookings;
    }

    public function save(Booking $booking): bool
    {
        $sql = "INSERT INTO bookings (user_id, room_id, check_in_date, check_out_date, number_of_guests, total_price, status, special_requests, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

        $this->database->query($sql, [
            $booking->getUserId(),
            $booking->getRoomId(),
            $booking->getCheckInDate()->format('Y-m-d'),
            $booking->getCheckOutDate()->format('Y-m-d'),
            $booking->getNumberOfGuests(),
            $booking->getTotalPrice(),
            $booking->getStatus(),
            $booking->getSpecialRequests()
        ]);

        return true;
    }

    public function update(Booking $booking): bool
    {
        $sql = "UPDATE bookings SET 
                user_id = ?, room_id = ?, check_in_date = ?, check_out_date = ?,
                number_of_guests = ?, total_price = ?, status = ?, special_requests = ?, updated_at = NOW()
                WHERE id = ?";

        $this->database->query($sql, [
            $booking->getUserId(),
            $booking->getRoomId(),
            $booking->getCheckInDate()->format('Y-m-d'),
            $booking->getCheckOutDate()->format('Y-m-d'),
            $booking->getNumberOfGuests(),
            $booking->getTotalPrice(),
            $booking->getStatus(),
            $booking->getSpecialRequests(),
            $booking->getId()
        ]);

        return true;
    }

    public function delete(int $id): bool
    {
        $this->database->query("DELETE FROM bookings WHERE id = ?", [$id]);
        return true;
    }

    public function hasConflict(int $roomId, \DateTime $checkIn, \DateTime $checkOut, ?int $excludeBookingId = null): bool
    {
        $sql = "SELECT COUNT(*) as count FROM bookings
                WHERE room_id = ? AND status IN ('pending', 'confirmed')
                AND (
                    (check_in_date <= ? AND check_out_date >= ?)
                    OR (check_in_date <= ? AND check_out_date >= ?)
                    OR (check_in_date >= ? AND check_out_date <= ?)
                )";

        $params = [
            $roomId,
            $checkIn->format('Y-m-d'), $checkIn->format('Y-m-d'),
            $checkOut->format('Y-m-d'), $checkOut->format('Y-m-d'),
            $checkIn->format('Y-m-d'), $checkOut->format('Y-m-d')
        ];

        if ($excludeBookingId) {
            $sql .= " AND id != ?";
            $params[] = $excludeBookingId;
        }

        $stmt = $this->database->query($sql, $params);
        $result = $stmt->fetch();

        return $result['count'] > 0;
    }

    private function mapToEntity(array $data): Booking
    {
        return new Booking(
            (int)$data['id'],
            (int)$data['user_id'],
            (int)$data['room_id'],
            new DateTime($data['check_in_date']),
            new DateTime($data['check_out_date']),
            (int)$data['number_of_guests'],
            (float)$data['total_price'],
            $data['status'],
            $data['special_requests']
        );
    }
}

