<?php

namespace App\Application\UseCases;
use App\Application\Validators\BookingValidator;
use App\Domain\Exceptions\InvalidBookingDataException;
use App\Domain\Interfaces\Repositories\BookingRepositoryInterface;
class CheckRoomAvailableUseCase
{
    private BookingRepositoryInterface $bookingRepo;
    public function __construct(BookingRepositoryInterface $bookingRepo){
        $this->bookingRepo = $bookingRepo;
    }

    public function execute(int $id, string $checkIn, string $checkOut): bool{
        return $this->bookingRepo->checkRoomAvailable($id, $checkIn, $checkOut);
    }
}