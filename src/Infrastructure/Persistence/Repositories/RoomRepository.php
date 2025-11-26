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
        $this->database->query("DELETE FROM bookings WHERE room_id = ? ", [$id]);
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

    /**
     * Get all rooms with detailed information including room type and images
     */
    public function findAllWithDetails(): array
    {
        $stmt = $this->database->query("
            SELECT 
                r.id as room_id, 
                r.room_number, 
                r.status,
                rt.name as room_type, 
                rt.capacity, 
                rt.amenities, 
                rt.price_per_night, 
                ri.id as image_id, 
                ri.image_url, 
                ri.storage_type, 
                ri.file_size, 
                ri.mime_type, 
                ri.is_primary as image_is_primary, 
                ri.display_order
            FROM rooms as r
            LEFT JOIN room_images as ri ON r.id = ri.room_id
            INNER JOIN room_types as rt ON r.room_type_id = rt.id
            ORDER BY r.id, ri.display_order
        ");

        return $this->mapToDetailedRooms($stmt->fetchAll());
    }

    /**
     * Get single room with detailed information including room type and images
     */
    public function findByIdWithDetails(int $id): ?array
    {
        $stmt = $this->database->query("
            SELECT 
                r.id as room_id, 
                r.room_number, 
                r.status,
                rt.name as room_type, 
                rt.capacity, 
                rt.amenities, 
                rt.price_per_night, 
                ri.id as image_id, 
                ri.image_url, 
                ri.storage_type, 
                ri.file_size, 
                ri.mime_type, 
                ri.is_primary as image_is_primary, 
                ri.display_order
            FROM rooms as r
            LEFT JOIN room_images as ri ON r.id = ri.room_id
            INNER JOIN room_types as rt ON r.room_type_id = rt.id
            WHERE r.id = ?
            ORDER BY ri.display_order
        ", [$id]);

        $results = $stmt->fetchAll();
        if (empty($results)) {
            return null;
        }

        $rooms = $this->mapToDetailedRooms($results);
        return $rooms[0] ?? null;
    }

    /**
     * Map database results to detailed room array structure
     */
    private function mapToDetailedRooms(array $results): array
    {
        $rooms = [];

        foreach ($results as $row) {
            $roomId = $row['room_id'];

            // Initialize room if not exists
            if (!isset($rooms[$roomId])) {
                // Parse amenities - handle both JSON and comma-separated string
                $amenities = [];
                if (!empty($row['amenities'])) {
                    // Try to decode as JSON first
                    $decoded = json_decode($row['amenities'], true);
                    if (is_array($decoded)) {
                        $amenities = $decoded;
                    } else {
                        // If not JSON, treat as comma-separated string
                        $amenities = array_map('trim', explode(',', $row['amenities']));
                    }
                }

                $rooms[$roomId] = [
                    'roomId' => $roomId,
                    'roomNumber' => $row['room_number'],
                    'status' => $row['status'],
                    'roomType' => $row['room_type'],
                    'capacity' => (int)$row['capacity'],
                    'amenities' => $amenities,
                    'pricePerNight' => (float)$row['price_per_night'],
                    'images' => []
                ];
            }

            // Add image if exists
            if ($row['image_id']) {
                $rooms[$roomId]['images'][] = [
                    'imageId' => $row['image_id'],
                    'imageUrl' => $row['image_url'],
                    'storageType' => $row['storage_type'],
                    'fileSize' => (int)$row['file_size'],
                    'mimeType' => $row['mime_type'],
                    'isPrimary' => (bool)$row['image_is_primary'],
                    'displayOrder' => (int)$row['display_order']
                ];
            }
        }

        return array_values($rooms);
    }
    private function mapToDetail(array $results): array
    {
        $rooms = [];

        foreach ($results as $row) {
            $roomId = $row['room_id'];

            // Initialize room if not exists
            if (!isset($rooms[$roomId])) {
                // Parse amenities - handle both JSON and comma-separated string
                $amenities = [];
                if (!empty($row['amenities'])) {
                    // Try to decode as JSON first
                    $decoded = json_decode($row['amenities'], true);
                    if (is_array($decoded)) {
                        $amenities = $decoded;
                    } else {
                        // If not JSON, treat as comma-separated string
                        $amenities = array_map('trim', explode(',', $row['amenities']));
                    }
                }

                $rooms[$roomId] = [
                    'roomId' => $roomId,
                    'roomNumber' => $row['room_number'],
                    'status' => $row['status'],
                    'roomType' => $row['room_type'],
                    'capacity' => (int)$row['capacity'],
                    'description' => $row['description'],
                    'amenities' => $amenities,
                    'pricePerNight' => (float)$row['price_per_night'],
                    'images' => []
                ];
            }

            // Add image if exists
            if ($row['image_id']) {
                $rooms[$roomId]['images'][] = [
                    'imageId' => $row['image_id'],
                    'imageUrl' => $row['image_url'],
                    'storageType' => $row['storage_type'],
                    'fileSize' => (int)$row['file_size'],
                    'mimeType' => $row['mime_type'],
                    'isPrimary' => (bool)$row['image_is_primary'],
                    'displayOrder' => (int)$row['display_order']
                ];
            }
        }

        return array_values($rooms);
    }
    public function details(int $id): ?array
    {
        $stmt = $this->database->query("
            SELECT 
                r.id as room_id, 
                r.room_number, 
                r.status,
                rt.name as room_type, 
                rt.description,
                rt.capacity, 
                rt.amenities, 
                rt.price_per_night, 
                ri.id as image_id, 
                ri.image_url, 
                ri.storage_type, 
                ri.file_size, 
                ri.mime_type, 
                ri.is_primary as image_is_primary, 
                ri.display_order
            FROM rooms as r
            LEFT JOIN room_images as ri ON r.id = ri.room_id
            INNER JOIN room_types as rt ON r.room_type_id = rt.id
            WHERE r.id = ?
            ORDER BY ri.display_order
        ", [$id]);

        $results = $stmt->fetchAll();
        if (empty($results)) {
            return null;
        }

        $rooms = $this->mapToDetail($results);
        return $rooms[0] ?? null;
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
