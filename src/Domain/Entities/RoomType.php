<?php

namespace App\Domain\Entities;

class RoomType
{
    private ?int $id;
    private string $name;
    private string $description;
    private int $capacity;
    private float $pricePerNight;
    private array $amenities;
    private ?string $createdAt;
    private ?string $updatedAt;

    public function __construct(
        ?int $id,
        string $name,
        string $description,
        int $capacity,
        float $pricePerNight,
        array $amenities,
        ?string $createdAt = null,
        ?string $updatedAt = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->capacity = $capacity;
        $this->pricePerNight = $pricePerNight;
        $this->amenities = $amenities;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getCapacity(): int
    {
        return $this->capacity;
    }

    public function getPricePerNight(): float
    {
        return $this->pricePerNight;
    }

    public function getAmenities(): array
    {
        return $this->amenities;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
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
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }
}

