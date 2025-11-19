<?php

namespace App\Application\UseCases;
use App\Domain\Entities\Booking;
use App\Domain\Exceptions\BookingNotFoundException;
use App\Domain\Interfaces\Repositories\BookingRepositoryInterface;
class FilterBookingByCheckInDateUseCase
{
    private BookingRepositoryInterface $repository;
    public function __construct(BookingRepositoryInterface $repository){
        $this->repository = $repository;
    }

    public function execute(string $checkIndate): array{
        return $this->repository->findByCheckInDate($checkIndate);
    }
}