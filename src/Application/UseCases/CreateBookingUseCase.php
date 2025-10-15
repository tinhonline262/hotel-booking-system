<?php

namespace App\Application\UseCases;

use App\Domain\Entities\Booking;
use App\Domain\Repositories\BookingRepositoryInterface;
use App\Domain\Repositories\RoomRepositoryInterface;
use DateTime;

/**
 * Create Booking Use Case
 */
class CreateBookingUseCase
{
    private BookingRepositoryInterface $bookingRepository;
    private RoomRepositoryInterface $roomRepository;

    public function __construct(
        BookingRepositoryInterface $bookingRepository,
        RoomRepositoryInterface $roomRepository
    ) {
        $this->bookingRepository = $bookingRepository;
        $this->roomRepository = $roomRepository;
    }

    public function execute(array $data): Booking
    {
        // Validate room exists and is available
        $room = $this->roomRepository->findById($data['room_id']);
        if (!$room) {
            throw new \Exception("Room not found");
        }

        if (!$room->isAvailable()) {
            throw new \Exception("Room is not available");
        }

        // Check for booking conflicts
        $checkIn = new DateTime($data['check_in_date']);
        $checkOut = new DateTime($data['check_out_date']);

        if ($this->bookingRepository->hasConflict($data['room_id'], $checkIn, $checkOut)) {
            throw new \Exception("Room is already booked for these dates");
        }

        // Validate capacity
        if ($data['number_of_guests'] > $room->getCapacity()) {
            throw new \Exception("Number of guests exceeds room capacity");
        }

        // Calculate total price
        $days = $checkIn->diff($checkOut)->days;
        $totalPrice = $room->getPricePerNight() * $days;

        // Create booking
        $booking = new Booking(
            null,
            $data['user_id'],
            $data['room_id'],
            $checkIn,
            $checkOut,
            $data['number_of_guests'],
            $totalPrice,
            'pending',
            $data['special_requests'] ?? null
        );

        $this->bookingRepository->save($booking);

        return $booking;
    }
}

