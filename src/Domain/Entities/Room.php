<?php

namespace App\Domain\Entities;

use DateTime;

/**
 * Room Entity (Aggregate Root)
 */
class Room
{
    private ?int $id;
    private string $roomNumber;
    private string $type;
    private string $description;
    private float $pricePerNight;
    private int $capacity;
    private int $floor;
    private array $amenities;
    private string $status; // available, occupied, maintenance
    private array $images;
    private DateTime $createdAt;
    private DateTime $updatedAt;

    public function __construct(
        ?int $id,
        string $roomNumber,
        string $type,
        string $description,
        float $pricePerNight,
        int $capacity,
        int $floor,
        array $amenities = [],
        string $status = 'available',
        array $images = []
    ) {
        $this->id = $id;
        $this->roomNumber = $roomNumber;
        $this->type = $type;
        $this->description = $description;
        $this->pricePerNight = $pricePerNight;
        $this->capacity = $capacity;
        $this->floor = $floor;
        $this->amenities = $amenities;
        $this->status = $status;
        $this->images = $images;
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getRoomNumber(): string { return $this->roomNumber; }
    public function getType(): string { return $this->type; }
    public function getDescription(): string { return $this->description; }
    public function getPricePerNight(): float { return $this->pricePerNight; }
    public function getCapacity(): int { return $this->capacity; }
    public function getFloor(): int { return $this->floor; }
    public function getAmenities(): array { return $this->amenities; }
    public function getStatus(): string { return $this->status; }
    public function getImages(): array { return $this->images; }
    public function getCreatedAt(): DateTime { return $this->createdAt; }
    public function getUpdatedAt(): DateTime { return $this->updatedAt; }

    // Business Logic
    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    public function setStatus(string $status): void
    {
        $allowedStatuses = ['available', 'occupied', 'maintenance'];
        if (!in_array($status, $allowedStatuses)) {
            throw new \InvalidArgumentException("Invalid status: {$status}");
        }
        $this->status = $status;
        $this->updatedAt = new DateTime();
    }

    public function updatePrice(float $newPrice): void
    {
        if ($newPrice <= 0) {
            throw new \InvalidArgumentException("Price must be greater than 0");
        }
        $this->pricePerNight = $newPrice;
        $this->updatedAt = new DateTime();
    }

    public function addAmenity(string $amenity): void
    {
        if (!in_array($amenity, $this->amenities)) {
            $this->amenities[] = $amenity;
            $this->updatedAt = new DateTime();
        }
    }

    public function addImage(string $imageUrl): void
    {
        $this->images[] = $imageUrl;
        $this->updatedAt = new DateTime();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'room_number' => $this->roomNumber,
            'type' => $this->type,
            'description' => $this->description,
            'price_per_night' => $this->pricePerNight,
            'capacity' => $this->capacity,
            'floor' => $this->floor,
            'amenities' => $this->amenities,
            'status' => $this->status,
            'images' => $this->images,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }
}

