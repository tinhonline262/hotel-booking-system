<?php
namespace App\Presentation\Controllers\Api;

use App\Application\UseCases\FindBookingByCodeUseCase;

class BookingApiController
{
    public function __construct(
        private FindBookingByCodeUseCase $findBookingUseCase
    ) {}

    /**
     * API: GET /api/booking-lookup?code=BK-123
     */
    public function lookup()
    {
        // 1. Lấy input
        $code = $_GET['code'] ?? '';

        // 2. Gọi UseCase (Logic cũ)
        $booking = $this->findBookingUseCase->execute($code);

        // 3. Trả về JSON
        header('Content-Type: application/json');
        
        if ($booking) {
            http_response_code(200);
            echo json_encode([
                'status' => 'success',
                'data' => $booking
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Không tìm thấy đơn đặt phòng với mã này.'
            ]);
        }
        exit;
    }
}