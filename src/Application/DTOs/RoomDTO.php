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
            $data['name'] ?? '',
            $data['description'] ?? '',
            (int)($data['capacity'] ?? 0),
            (float)($data['pricePerNight'] ?? 0),
            $data['amenities'] ?? []
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'capacity' => $this->capacity,
            'pricePerNight' => $this->pricePerNight,
            'amenities' => $this->amenities,
        ];
    }
}

