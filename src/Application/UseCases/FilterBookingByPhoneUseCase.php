<?php

namespace App\Application\UseCases;
use App\Domain\Entities\Booking;
use App\Domain\Interfaces\Repositories\BookingRepositoryInterface;
class FilterBookingByPhoneUseCase
{
    private BookingRepositoryInterface $repository;
    public function __construct(BookingRepositoryInterface $repository){
        $this->repository = $repository;
    }
    public function execute(string $phone): array{
        return $this->repository->findByPhone($phone);
    }
}