<?php

namespace App\Application\UseCases;
use App\Domain\Entities\Booking;
use App\Domain\Interfaces\Repositories\BookingRepositoryInterface;
class FilterBookingByName
{
    private BookingRepositoryInterface $repository;
    public function __construct(BookingRepositoryInterface $repository){
        $this->repository = $repository;
    }
    public function execute(string $name): array{
        return $this->repository->findByName($name);
    }
}