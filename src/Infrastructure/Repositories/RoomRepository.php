<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Room;
use App\Domain\Repositories\RoomRepositoryInterface;
use App\Core\Database\Database;
use DateTime;

/**
 * Room Repository Implementation
 */
class RoomRepository implements RoomRepositoryInterface
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function findById(int $id): ?Room
    {
        $stmt = $this->database->query(
            "SELECT * FROM rooms WHERE id = ?",
            [$id]
        );

        $data = $stmt->fetch();
        return $data ? $this->mapToEntity($data) : null;
    }

    public function findAll(): array
    {
        $stmt = $this->database->query("SELECT * FROM rooms ORDER BY room_number");
        $rooms = [];

        while ($data = $stmt->fetch()) {
            $rooms[] = $this->mapToEntity($data);
        }

        return $rooms;
    }

    public function findAvailable(): array
    {
        $stmt = $this->database->query(
            "SELECT * FROM rooms WHERE status = ? ORDER BY room_number",
            ['available']
        );
        $rooms = [];

        while ($data = $stmt->fetch()) {
            $rooms[] = $this->mapToEntity($data);
        }

        return $rooms;
    }

    public function findByType(string $type): array
    {
        $stmt = $this->database->query(
            "SELECT * FROM rooms WHERE type = ? ORDER BY room_number",
            [$type]
        );
        $rooms = [];

        while ($data = $stmt->fetch()) {
            $rooms[] = $this->mapToEntity($data);
        }

        return $rooms;
    }

    public function findAvailableByDateRange(\DateTime $checkIn, \DateTime $checkOut): array
    {
        $sql = "SELECT r.* FROM rooms r
                WHERE r.status = 'available'
                AND r.id NOT IN (
                    SELECT room_id FROM bookings
                    WHERE status IN ('pending', 'confirmed')
                    AND (
                        (check_in_date <= ? AND check_out_date >= ?)
                        OR (check_in_date <= ? AND check_out_date >= ?)
                        OR (check_in_date >= ? AND check_out_date <= ?)
                    )
                )
                ORDER BY r.room_number";

        $checkInStr = $checkIn->format('Y-m-d');
        $checkOutStr = $checkOut->format('Y-m-d');

        $stmt = $this->database->query($sql, [
            $checkInStr, $checkInStr,
            $checkOutStr, $checkOutStr,
            $checkInStr, $checkOutStr
        ]);

        $rooms = [];
        while ($data = $stmt->fetch()) {
            $rooms[] = $this->mapToEntity($data);
        }

        return $rooms;
    }

    public function save(Room $room): bool
    {
        $sql = "INSERT INTO rooms (room_number,room_type_id, status, created_at, updated_at)
                VALUES (?, ?, ?, NOW(), NOW())";

        $this->database->query($sql, [
            $room->getRoomNumber(),
            $room->getRoomTypeId(),
            $room->getCreatedAt(),
            $room->getUpdatedAt(),
            $room->getId(),
            $room->getStatus()
        ]);

        return true;
    }

    public function update(Room $room, int $id): bool
    {
        $sql = "UPDATE rooms SET 
                room_number = ?, room_type_id = ?,  status = ?, images = ?, updated_at = NOW()
                WHERE id = ?";

        $this->database->query($sql, [
            $room->getRoomNumber(),
            $room->getRoomTypeId(),
            $room->getCreatedAt(),
            $room->getUpdatedAt(),
            $room->getStatus(),
            $room->getId()
        ]);

        return true;
    }

    public function delete(int $id): bool
    {
        $this->database->query("DELETE FROM rooms WHERE id = ?", [$id]);
        return true;
    }

    public function exists(int $id): bool
    {
        $stmt = $this->database->query("SELECT COUNT(*) as count FROM rooms WHERE id = ?", [$id]);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }

    private function mapToEntity(array $data): Room
    {
        return new Room(
            (int)$data['id'],
            $data['room_number'],
            (int)$data['room_type_id'],
            $data['status'],
            $data['created_at'],
            $data['updated_at']
        );
    }
}

