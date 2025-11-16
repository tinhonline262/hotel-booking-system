<?php

namespace App\Application\DTOs;

class RoomDTO
{
    public ?int $id;
    public string $room_number;
    public ?int $room_type_id ;
    public string $status;
    public function __construct(
        ?int $id,
        string $room_number,
        ?int $room_type_id,
        string $status
    ) {
        $this->id = $id;
        $this->room_number = $room_number;
        $this->room_type_id = $room_type_id;
        $this->status = $status;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['room_number'] ?? '',
            $data['room_type_id'] ?? null,
            $data['status'] ?? ''
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'room_number' => $this->room_number,
            'room_type_id' => $this->room_type_id,
            'status' => $this->status
        ];
    }
}

