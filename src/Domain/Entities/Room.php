<?php

namespace App\Domain\Entities;

use DateTime;

/**
 * Room Entity (Aggregate Root)
 */
class Room
{
    private ?int $id;
    private string $room_number;
    private ?int $room_type_id;
    private string $status;
    private DateTime $createdAt;
    private DateTime $updatedAt;

    public function __construct(
        ?int $id,
        string $room_number,
        ?int $room_type_id,
        DateTime $createdAt,
        DateTime $updatedAt,
        string $status = 'available'
    ) {
        $this->id = $id;
        $this->room_number = $room_number;
        $this->room_type_id = $room_type_id;
        $this->status = $status;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getRoomNumber(): string { return $this->room_number; }
    public function getRoomTypeId(): ?int { return $this->room_type_id; }
    public function getStatus(): string { return $this->status; }
    public function getCreatedAt(): DateTime { return $this->createdAt; }
    public function getUpdatedAt(): DateTime { return $this->updatedAt; }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'room_number' => $this->room_number,
            'room_type_id' => $this->room_type_id,
            'status' => $this->status,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }
}

