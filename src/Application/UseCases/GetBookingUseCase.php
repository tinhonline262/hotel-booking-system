<?php

namespace App\Application\UseCases;
use App\Domain\Exceptions\BookingNotFoundException;
use App\Domain\Interfaces\Repositories\BookingRepositoryInterface;
use App\Application\DTOs\BookingDTO;
use App\Domain\Entities\Booking;
class GetBookingUseCase
{
    private BookingRepositoryInterface $repository;

    public function __construct(BookingRepositoryInterface $repository){
        $this->repository = $repository;
    }

    /**
     * @throws BookingNotFoundException
     */
    public function execute(int $id) : Booking{
        $booking = $this->repository->findById($id);
        if(!$booking){
            throw new BookingNotFoundException($id);
        }
        return $booking;
    }
}