<?php
namespace App\Presentation\Controllers;

use App\Application\UseCases\GetFeaturedRoomTypesUseCase;
use App\Core\Template\TemplateEngine; // (Hoặc Interface của bạn)

class HomeController
{
    public function __construct(
        private GetFeaturedRoomTypesUseCase $getFeaturedRoomsUseCase,
        private TemplateEngine $templateEngine // (Hoặc Interface của bạn)
    ) {}

    /**
     * Hiển thị trang chủ
     */
    public function index()
    {
        $featuredRooms = $this->getFeaturedRoomsUseCase->execute(3); // Lấy 3 phòng

        $data = [
            'hotelName' => 'The Gemini Hotel', // (Nên lấy từ config)
            'featuredRooms' => $featuredRooms,
            'pageTitle' => 'Trang chủ'
        ];
        
        // Render view (trỏ đến 'Views/pages/home.php')
        echo $this->templateEngine->render('pages/home', $data);
    }
}