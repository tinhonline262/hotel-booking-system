<?php

namespace App\Application\UseCases;
use App\Domain\Entities\Booking;
use App\Domain\Interfaces\Repositories\BookingRepositoryInterface;
class FilterBookingByEmailUseCase
{
    private BookingRepositoryInterface $repository;
    public function __construct(BookingRepositoryInterface $repository){
        $this->repository = $repository;
    }
    public function execute(string $email): Booking{
        return $this->repository->findByEmail($email);
    }
}