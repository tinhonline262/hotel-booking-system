<?php
namespace App\Application\UseCases;

use App\Domain\Repositories\BookingRepositoryInterface;
use App\Application\Interfaces\ICacheService;

class FindBookingByCodeUseCase
{
    // 1. Khai báo thuộc tính rõ ràng ở đầu class
    private BookingRepositoryInterface $bookingRepo;
    private ICacheService $cacheService;

    // 2. Hàm khởi tạo (Constructor)
    public function __construct(
        BookingRepositoryInterface $bookingRepo,
        ICacheService $cacheService
    ) {
        // 3. Gán giá trị vào thuộc tính
        $this->bookingRepo = $bookingRepo;
        $this->cacheService = $cacheService;
    }

    public function execute(string $code): ?array
    {
        $cleanCode = trim(htmlspecialchars($code));
        if (empty($cleanCode)) {
            return null;
        }

        $cacheKey = "booking_lookup_{$cleanCode}";
        
        // Bây giờ $this->cacheService sẽ được nhận diện chính xác
        $cachedData = $this->cacheService->get($cacheKey);
        
        if ($cachedData !== null) {
            return $cachedData;
        }

        $booking = $this->bookingRepo->findByCode($cleanCode);

        if ($booking) {
            $this->cacheService->set($cacheKey, $booking, 600);
        }

        return $booking;
    }
}