<?php

namespace App\Application\UseCases;
use App\Domain\Entities\Booking;
use App\Domain\Interfaces\Repositories\BookingRepositoryInterface;
class FilterBookingByCodeUseCase
{
    private BookingRepositoryInterface $repository;
    public function __construct(BookingRepositoryInterface $repository){
        $this->repository = $repository;
    }
    public function execute(string $code):array{
       return $this->repository->findByCode($code);
    }
}