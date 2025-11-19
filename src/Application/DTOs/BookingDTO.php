<?php

namespace App\Application\DTOs;

class BookingDTO
{
    public ?int $id;
    public string $bookingCode;
    public int $roomId;
    public string $customerName;
    public string $customerEmail;
    public string $customerPhone;
    public string $checkInDate;
    public string $checkOutDate;
    public int $numGuests;
    public float $totalPrice;
    public string $status;
    public ?string $specialRequests;

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
        ?string $specialRequests = null
    ){
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
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['booking_code'] ?? '',
            $data['room_id'] ?? '',
            $data['customer_name'] ?? '',
            $data['customer_email'] ?? '',
            $data['customer_phone'] ?? '',
            $data['check_in_date'] ?? '',
            $data['check_out_date'] ?? '',
            $data['num_guests'] ?? 1,
            $data['total_price'] ?? 0,
            $data['status'] ?? 'pending',
            $data['special_requests'] ?? null
        );
    }

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
        ];
    }

}