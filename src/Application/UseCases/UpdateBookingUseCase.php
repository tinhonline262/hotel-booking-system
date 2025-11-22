<?php

namespace App\Application\UseCases;
use App\Application\Validators\BookingValidator;
use App\Domain\Entities\Booking;
use App\Domain\Interfaces\Repositories\BookingRepositoryInterface;
use App\Domain\Exceptions\InvalidBookingDataException;
use App\Domain\Exceptions\BookingNotFoundException;
use App\Application\DTOs\BookingDTO;
class UpdateBookingUseCase
{
    private BookingRepositoryInterface $repository;
    private BookingValidator $validator;

    public function __construct(BookingRepositoryInterface $repository, BookingValidator $validator)
    {
        $this->repository = $repository;
        $this->validator = $validator;
    }

    /**
     * @throws BookingNotFoundException
     * @throws InvalidBookingDataException
     */
    public function execute(BookingDTO $bookingDTO, int $id): bool
    {
        if(!$this->repository->exists($id))
        {
            throw new BookingNotFoundException($id);
        }

        $error = $this->validator->validateUpdate($bookingDTO->toArray(),$id);
        if(!empty($error))
        {
            throw new InvalidBookingDataException($error);
        }

        $booking = new Booking(
            $id,
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
        return $this->repository->update($booking,$id);
    }
}