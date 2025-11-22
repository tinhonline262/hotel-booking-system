<?php

namespace App\Application\UseCases;
use App\Domain\Exceptions\BookingNotFoundException;
use App\Domain\Interfaces\Repositories\BookingRepositoryInterface;
class DeleteBookingUseCase
{
    private BookingRepositoryInterface $repository;
    public function __construct(BookingRepositoryInterface $repository){
        $this->repository = $repository;
    }

    /**
     * @throws BookingNotFoundException
     */
    public function execute(int $id) :bool{
        if(!$this->repository->exists($id)){
            throw new BookingNotFoundException($id);
        }
        return $this->repository->delete($id);
    }
}