<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Core\Database\Database;
use App\Domain\Entities\RoomImage;
use App\Domain\Exceptions\ImageUpdateDisplayOrderException;
use App\Domain\Exceptions\RoomNotFoundException;
use App\Domain\Interfaces\Repositories\RoomImageRepositoryInterface;

class RoomImageRepository implements RoomImageRepositoryInterface
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function save(RoomImage $image): ?int
    {
        $sql = "INSERT INTO room_images 
            (room_id, image_url, storage_type, cloudinary_public_id, file_size, mime_type, display_order, is_primary, alt_text) 
            VALUES (:room_id, :image_url, :storage_type, :cloudinary_public_id, :file_size, :mime_type, :display_order, :is_primary, :alt_text)";

        $params = [
            'room_id' => $image->getRoomId(),
            'image_url' => $image->getImageUrl(),
            'storage_type' => $image->getStorageType(),
            'cloudinary_public_id' => $image->getCloudinaryPublicId(),
            'file_size' => $image->getFileSize(),
            'mime_type' => $image->getMimeType(),
            'display_order' => $image->getDisplayOrder(),
            'is_primary' => $image->isPrimary() ? 1 : 0,
            'alt_text' => $image->getAltText(),
        ];

        try {
            $this->db->query($sql, $params);
            return (int)$this->db->lastInsertId();
        } catch (\Exception $e) {
            return null;
        }
    }

    public function findById(int $id): ?RoomImage
    {
        $sql = "SELECT * FROM room_images WHERE id = :id";
        $stmt = $this->db->query($sql, ['id' => $id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return $row ? $this->mapToEntity($row) : null;
    }

    public function findByRoomId(int $roomId): array
    {
        $sql = "SELECT * FROM room_images WHERE room_id = :room_id ORDER BY display_order ASC";
        $stmt = $this->db->query($sql, ['room_id' => $roomId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        return array_map([$this, 'mapToEntity'], $rows);
    }

    public function findPrimaryByRoomId(int $roomId): ?RoomImage
    {
        $sql = "SELECT * FROM room_images WHERE room_id = :room_id AND is_primary = 1 LIMIT 1";
        $stmt = $this->db->query($sql, ['room_id' => $roomId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return $row ? $this->mapToEntity($row) : null;
    }

    public function update(RoomImage $image): bool
    {
        $sql = "UPDATE room_images 
            SET display_order = :display_order, is_primary = :is_primary, alt_text = :alt_text 
            WHERE id = :id";

        $params = [
            'display_order' => $image->getDisplayOrder(),
            'is_primary' => $image->isPrimary() ? 1 : 0,
            'alt_text' => $image->getAltText(),
            'id' => $image->getId(),
        ];

        try {
            $this->db->query($sql, $params);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM room_images WHERE id = :id";

        try {
            $this->db->query($sql, ['id' => $id]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function setPrimary(int $id, int $roomId): bool
    {
        $this->db->beginTransaction();

        try {
            // Remove primary from all images of this room
            $sql1 = "UPDATE room_images SET is_primary = 0 WHERE room_id = :room_id";
            $this->db->query($sql1, ['room_id' => $roomId]);

            // Set new primary
            $sql2 = "UPDATE room_images SET is_primary = 1 WHERE id = :id AND room_id = :room_id";
            $this->db->query($sql2, ['id' => $id, 'room_id' => $roomId]);

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollback();
            return false;
        }
    }

    public function updateDisplayOrders(array $orders): bool
    {
        $this->db->beginTransaction();

        try {
            $sql = "UPDATE room_images SET display_order = :order WHERE id = :id";

            foreach ($orders as $id => $order) {
                $this->db->query($sql, ['id' => $order['imageId'], 'order' => $order['displayOrder']]);
            }
            
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollback();
            throw new ImageUpdateDisplayOrderException([$e->getMessage()]);
        }
    }

    public function getTotalStorageUsed(string $storageType = 'local'): int
    {
        $sql = "SELECT COALESCE(SUM(file_size), 0) as total FROM room_images WHERE storage_type = :storage_type";
        $stmt = $this->db->query($sql, ['storage_type' => $storageType]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return (int)($row['total'] ?? 0);
    }

    public function deleteByRoomId(int $roomId): bool
    {
        $sql = "DELETE FROM room_images WHERE room_id = :room_id";

        try {
            $this->db->query($sql, ['room_id' => $roomId]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function mapToEntity(array $row): RoomImage
    {
        return new RoomImage(
            (int)$row['id'],
            (int)$row['room_id'],
            $row['image_url'],
            $row['storage_type'] ?? 'local',
            $row['cloudinary_public_id'] ?? null,
            (int)($row['file_size'] ?? 0),
            $row['mime_type'] ?? 'image/jpeg',
            (int)$row['display_order'],
            (bool)$row['is_primary'],
            $row['alt_text'] ?? null,
            $row['created_at'] ?? null,
            $row['updated_at'] ?? null
        );
    }
}

