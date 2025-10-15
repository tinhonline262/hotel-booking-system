<?php

namespace App\Domain\Entities;

use DateTime;

/**
 * Booking Entity (Aggregate Root)
 */
class Booking
{
    private ?int $id;
    private int $userId;
    private int $roomId;
    private DateTime $checkInDate;
    private DateTime $checkOutDate;
    private int $numberOfGuests;
    private float $totalPrice;
    private string $status; // pending, confirmed, cancelled, completed
    private ?string $specialRequests;
    private DateTime $createdAt;
    private DateTime $updatedAt;

    public function __construct(
        ?int $id,
        int $userId,
        int $roomId,
        DateTime $checkInDate,
        DateTime $checkOutDate,
        int $numberOfGuests,
        float $totalPrice,
        string $status = 'pending',
        ?string $specialRequests = null
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->roomId = $roomId;
        $this->checkInDate = $checkInDate;
        $this->checkOutDate = $checkOutDate;
        $this->numberOfGuests = $numberOfGuests;
        $this->totalPrice = $totalPrice;
        $this->status = $status;
        $this->specialRequests = $specialRequests;
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();

        $this->validate();
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getUserId(): int { return $this->userId; }
    public function getRoomId(): int { return $this->roomId; }
    public function getCheckInDate(): DateTime { return $this->checkInDate; }
    public function getCheckOutDate(): DateTime { return $this->checkOutDate; }
    public function getNumberOfGuests(): int { return $this->numberOfGuests; }
    public function getTotalPrice(): float { return $this->totalPrice; }
    public function getStatus(): string { return $this->status; }
    public function getSpecialRequests(): ?string { return $this->specialRequests; }
    public function getCreatedAt(): DateTime { return $this->createdAt; }
    public function getUpdatedAt(): DateTime { return $this->updatedAt; }

    // Business Logic
    private function validate(): void
    {
        if ($this->checkInDate >= $this->checkOutDate) {
            throw new \InvalidArgumentException("Check-out date must be after check-in date");
        }

        if ($this->numberOfGuests <= 0) {
            throw new \InvalidArgumentException("Number of guests must be greater than 0");
        }

        if ($this->totalPrice < 0) {
            throw new \InvalidArgumentException("Total price cannot be negative");
        }
    }

    public function confirm(): void
    {
        if ($this->status !== 'pending') {
            throw new \DomainException("Only pending bookings can be confirmed");
        }
        $this->status = 'confirmed';
        $this->updatedAt = new DateTime();
    }

    public function cancel(): void
    {
        if ($this->status === 'completed' || $this->status === 'cancelled') {
            throw new \DomainException("Cannot cancel completed or already cancelled bookings");
        }
        $this->status = 'cancelled';
        $this->updatedAt = new DateTime();
    }

    public function complete(): void
    {
        if ($this->status !== 'confirmed') {
            throw new \DomainException("Only confirmed bookings can be completed");
        }
        $this->status = 'completed';
        $this->updatedAt = new DateTime();
    }

    public function getDurationInDays(): int
    {
        return $this->checkInDate->diff($this->checkOutDate)->days;
    }

    public function isPast(): bool
    {
        return $this->checkOutDate < new DateTime();
    }

    public function isCurrent(): bool
    {
        $now = new DateTime();
        return $this->checkInDate <= $now && $this->checkOutDate >= $now;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'room_id' => $this->roomId,
            'check_in_date' => $this->checkInDate->format('Y-m-d'),
            'check_out_date' => $this->checkOutDate->format('Y-m-d'),
            'number_of_guests' => $this->numberOfGuests,
            'total_price' => $this->totalPrice,
            'status' => $this->status,
            'special_requests' => $this->specialRequests,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }
}

