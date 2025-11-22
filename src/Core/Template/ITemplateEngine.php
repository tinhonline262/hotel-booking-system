<?php
namespace App\Core\Template;

interface ITemplateEngine
{
    /**
     * Render một view
     * @param string $templateName Tên view (ví dụ: 'pages/home')
     * @param array $data Dữ liệu truyền vào view
     */
    public function render(string $templateName, array $data = []): string;
}