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

    /**
     * Tìm booking theo code và trả về dạng array
     * Trả về array chứa 1 booking nếu tìm thấy, hoặc array rỗng nếu không tìm thấy
     *
     * @param string $code
     * @return array
     */
    public function execute(string $code): array
    {
        $cleanCode = trim(strtoupper($code));

        if (empty($cleanCode)) {
            return [];
        }

        $booking = $this->repository->findByCode($cleanCode);

        // Nếu tìm thấy, trả về array chứa 1 booking
        // Nếu không tìm thấy, trả về array rỗng
        return $booking ? [$booking] : [];
    }
}