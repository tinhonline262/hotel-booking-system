<?php
namespace App\Core\Template;

class TemplateEngine implements ITemplateEngine
{
    private string $viewsPath;

    public function __construct(string $viewsPath)
    {
        // Xóa dấu gạch chéo thừa ở cuối đường dẫn nếu có
        $this->viewsPath = rtrim($viewsPath, '/\\');
    }

    public function render(string $templateName, array $data = []): string
    {
        // Chuyển đổi dấu chấm thành dấu gạch chéo (vd: pages.home -> pages/home)
        $filePath = $this->viewsPath . '/' . str_replace('.', '/', $templateName) . '.php';

        if (!file_exists($filePath)) {
            throw new \Exception("View file not found: {$filePath}");
        }

        // Giải nén mảng data thành các biến riêng lẻ để dùng trong view
        extract($data, EXTR_SKIP);

        // Bắt đầu bộ đệm đầu ra (Output Buffering)
        ob_start();
        
        // Nhúng file view
        include $filePath;
        
        // Lấy nội dung và xóa bộ đệm
        return ob_get_clean();
    }
}