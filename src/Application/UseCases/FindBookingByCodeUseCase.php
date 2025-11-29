<?php
namespace App\Application\UseCases;

use App\Application\Interfaces\ICacheService;
use App\Domain\Interfaces\Repositories\BookingRepositoryInterface;
use App\Domain\Entities\Booking;

class FindBookingByCodeUseCase
{
    private BookingRepositoryInterface $bookingRepository;
    private ICacheService $cacheService;

    public function __construct(
        BookingRepositoryInterface $bookingRepository,
        ICacheService $cacheService
    ) {
        $this->bookingRepository = $bookingRepository;
        $this->cacheService = $cacheService;
    }

    /**
     * Tìm booking theo mã booking code
     *
     * @param string $code Mã booking cần tìm
     * @return array|null Trả về thông tin booking dạng array hoặc null nếu không tìm thấy
     */
    public function execute(string $code): ?array
    {
        // Validate và làm sạch input
        $cleanCode = trim(strtoupper($code));

        if (empty($cleanCode)) {
            return null;
        }

        // Tạo cache key
        $cacheKey = "booking_code_{$cleanCode}";

        // Kiểm tra cache trước
        $cachedData = $this->cacheService->get($cacheKey);
        if ($cachedData !== null) {
            return $cachedData;
        }

        // Tìm booking từ database
        $booking = $this->bookingRepository->findByCode($cleanCode);

        if (!$booking) {
            return null;
        }

        // Convert Booking entity sang array để trả về
        $bookingData = $this->bookingToArray($booking);

        // Lưu vào cache (10 phút)
        $this->cacheService->set($cacheKey, $bookingData, 600);

        return $bookingData;
    }

    /**
     * Convert Booking entity sang array
     */
    private function bookingToArray(Booking $booking): array
    {
        return [
            'id' => $booking->getId(),
            'booking_code' => $booking->getBookingCode(),
            'room_id' => $booking->getRoomId(),
            'customer_name' => $booking->getCustomerName(),
            'customer_email' => $booking->getCustomerEmail(),
            'customer_phone' => $booking->getCustomerPhone(),
            'check_in_date' => $booking->getCheckInDate(),
            'check_out_date' => $booking->getCheckOutDate(),
            'num_guests' => $booking->getNumGuests(),
            'total_price' => $booking->getTotalPrice(),
            'status' => $booking->getStatus(),
            'special_requests' => $booking->getSpecialRequests(),
        ];
    }
}