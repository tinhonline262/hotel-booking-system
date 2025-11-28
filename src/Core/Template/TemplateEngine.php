<?php
namespace App\Core\Template;

class TemplateEngine implements ITemplateEngine
{
    private string $viewsPath;

    public function __construct(string $viewsPath)
    {
        $this->viewsPath = rtrim($viewsPath, '/\\');
    }

    public function render(string $templateName, array $data = []): string
    {
        // Chuyển 'pages/home' thành 'pages/home.php'
        $filePath = $this->viewsPath . '/' . str_replace('.', '/', $templateName) . '.php';

        if (!file_exists($filePath)) {
            // (Bạn nên tạo một Exception tùy chỉnh cho việc này)
            throw new \Exception("View file not found: {$filePath}");
        }

        // Biến mảng $data thành các biến riêng lẻ
        // ví dụ: $data['hotelName'] sẽ trở thành biến $hotelName
        extract($data, EXTR_SKIP);

        // Bắt đầu bộ đệm đầu ra
        ob_start();
        
        // Tải tệp view (tệp này sẽ có thể truy cập $hotelName)
        include $filePath;
        
        // Lấy nội dung bộ đệm và xóa nó
        return ob_get_clean();
    }
}