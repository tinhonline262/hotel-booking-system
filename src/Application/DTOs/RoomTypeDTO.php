<?php

namespace App\Application\DTOs;

class RoomTypeDTO
{
    public ?int $id;
    public string $name;
    public string $description;
    public int $capacity;
    public float $pricePerNight;
    public array $amenities;

    public function __construct(
        ?int $id,
        string $name,
        string $description,
        int $capacity,
        float $pricePerNight,
        array $amenities
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->capacity = $capacity;
        $this->pricePerNight = $pricePerNight;
        $this->amenities = $amenities;
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

