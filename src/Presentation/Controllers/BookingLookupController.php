<?php
namespace App\Presentation\Controllers;

use App\Application\UseCases\FindBookingByCodeUseCase;
use App\Core\Template\ITemplateEngine;

class BookingLookupController
{
    public function __construct(
        private FindBookingByCodeUseCase $findBookingUseCase,
        private ITemplateEngine $templateEngine
    ) {}

    public function index()
    {
        echo $this->templateEngine->render('pages/booking-lookup', [
            'pageTitle' => 'Tra cứu đặt phòng',
            'booking' => null,
            'error' => null,
            'searchCode' => ''
        ]);
    }

    public function search()
    {
        $code = $_GET['code'] ?? '';
        $booking = null;
        $error = null;

        if (empty($code)) {
            $error = 'Vui lòng nhập mã booking.';
        } else {
            $booking = $this->findBookingUseCase->execute($code);
            if (!$booking) {
                $error = 'Không tìm thấy thông tin đặt phòng với mã này.';
            }
        }

        echo $this->templateEngine->render('pages/booking-lookup', [
            'pageTitle' => 'Kết quả tra cứu',
            'booking' => $booking,
            'error' => $error,
            'searchCode' => $code
        ]);
    }
}