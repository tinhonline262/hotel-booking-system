<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Core\Database\Database;
use App\Domain\Entities\Room;
use App\Domain\Interfaces\Repositories\RoomRepositoryInterface;

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

    public function findByStatus(string $status): array
    {
        $stmt = $this->database->query(
            "SELECT * FROM rooms WHERE status = ? ORDER BY room_number",
            [$status]
        );
        $rooms = [];

        while ($data = $stmt->fetch()) {
            $rooms[] = $this->mapToEntity($data);
        }

        return $rooms;
    }
    public function save(Room $newRoom): bool
    {
        $stmt = $this->database->query("INSERT INTO rooms 
                (room_number, room_type_id, status) VALUES (?, ?, ?)", [
                    $newRoom->getRoomNumber(),
                    $newRoom->getRoomTypeId(),
                    $newRoom->getStatus()
        ]);
        return true;
    }

    public function update(Room $updateRoom, int $id): bool
    {
        $stmt = $this->database->query("UPDATE rooms SET 
                room_number = ?, room_type_id = ?,  status = ?
                WHERE id = ?", [
            $updateRoom->getRoomNumber(),
            $updateRoom->getRoomTypeId(),
            $updateRoom->getStatus(),
            $id
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
    public function findRoomNumber(string $roomNumber): ?Room
    {
        $stmt = $this->database->query("SELECT * FROM rooms WHERE room_number = ?", [$roomNumber]);
        $result = $stmt->fetch();
        return $result ? $this->mapToEntity($result) : null;
    }
    private function mapToEntity(array $data): Room
    {
        return new Room(
            $data['id'],
            $data['room_number'],
            $data['room_type_id'],
            $data['status'],
            $data['created_at'],
            $data['updated_at']
        );
    }
}

