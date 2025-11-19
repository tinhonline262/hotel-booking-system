<?php

namespace App\Domain\Entities;

/**
 * BookingController Entity
 */
class Booking
{
    private ?int $id;
    private string $bookingCode;
    private int $roomId;
    private string $customerName;
    private string $customerEmail;
    private string $customerPhone;
    private string $checkInDate;
    private string $checkOutDate;
    private int $numGuests;
    private float $totalPrice;
    private string $status;
    private ?string $specialRequests;
    private ?string $createdAt;
    private ?string $updatedAt;

    public function __construct(
        ?int $id,
        string $bookingCode,
        int $roomId,
        string $customerName,
        string $customerEmail,
        string $customerPhone,
        string $checkInDate,
        string $checkOutDate,
        int $numGuests,
        float $totalPrice,
        string $status = 'pending',
        ?string $specialRequests = null,
        ?string $createdAt = null,
        ?string $updatedAt = null
    ) {
        $this->id = $id;
        $this->bookingCode = $bookingCode;
        $this->roomId = $roomId;
        $this->customerName = $customerName;
        $this->customerEmail = $customerEmail;
        $this->customerPhone = $customerPhone;
        $this->checkInDate = $checkInDate;
        $this->checkOutDate = $checkOutDate;
        $this->numGuests = $numGuests;
        $this->totalPrice = $totalPrice;
        $this->status = $status;
        $this->specialRequests = $specialRequests;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getBookingCode(): string { return $this->bookingCode; }
    public function getRoomId(): int { return $this->roomId; }
    public function getCustomerName(): string { return $this->customerName; }
    public function getCustomerEmail(): string { return $this->customerEmail; }
    public function getCustomerPhone(): string { return $this->customerPhone; }
    public function getCheckInDate(): string { return $this->checkInDate; }
    public function getCheckOutDate(): string { return $this->checkOutDate; }
    public function getNumGuests(): int { return $this->numGuests; }
    public function getTotalPrice(): float { return $this->totalPrice; }
    public function getStatus(): string { return $this->status; }
    public function getSpecialRequests(): ?string { return $this->specialRequests; }
    public function getCreatedAt(): ?string { return $this->createdAt; }
    public function getUpdatedAt(): ?string { return $this->updatedAt; }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'booking_code' => $this->bookingCode,
            'room_id' => $this->roomId,
            'customer_name' => $this->customerName,
            'customer_email' => $this->customerEmail,
            'customer_phone' => $this->customerPhone,
            'check_in_date' => $this->checkInDate,
            'check_out_date' => $this->checkOutDate,
            'num_guests' => $this->numGuests,
            'total_price' => $this->totalPrice,
            'status' => $this->status,
            'special_requests' => $this->specialRequests,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}