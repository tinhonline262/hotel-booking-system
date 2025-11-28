<?php

namespace App\Application\UseCases;
use App\Application\DTOs\BookingDTO;
use App\Application\Validators\BookingValidator;
use App\Domain\Entities\Booking;
use App\Domain\Exceptions\InvalidBookingDataException;
use App\Domain\Interfaces\Repositories\BookingRepositoryInterface;
class CreateBookingUseCase
{
    private BookingRepositoryInterface $repository;
    private BookingValidator $validator;

    public function __construct(BookingRepositoryInterface $repository, BookingValidator $validator){
        $this->repository = $repository;
        $this->validator = $validator;
    }

    /**
     * @throws InvalidBookingDataException
     */
    public function execute (BookingDTO $bookingDTO): bool{
        $errors = $this->validator->validate($bookingDTO->toArray());
        if(!empty($errors)){
            throw new InvalidBookingDataException($errors);
        }

        $booking = new Booking(
            null,
            $bookingDTO->bookingCode,
            $bookingDTO->roomId,
            $bookingDTO->customerName,
            $bookingDTO->customerEmail,
            $bookingDTO->customerPhone,
            $bookingDTO->checkInDate,
            $bookingDTO->checkOutDate,
            $bookingDTO->numGuests,
            $bookingDTO->totalPrice,
            $bookingDTO->status,
            $bookingDTO->specialRequests
        );
        return $this->repository->save($booking);
    }
}