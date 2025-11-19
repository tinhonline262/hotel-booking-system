<?php

namespace App\Application\UseCases;
use App\Domain\Interfaces\Repositories\BookingRepositoryInterface;
class GetAllBookingUseCase
{
    private BookingRepositoryInterface $repository;

    public function __construct(BookingRepositoryInterface $repository){
        $this->repository = $repository;
    }

    public function execute() : array{

        return $this->repository->findAll();
    }
}