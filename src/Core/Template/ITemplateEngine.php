<?php
namespace App\Core\Template;

interface ITemplateEngine
{
    /**
     * Render một tệp view
     * @param string $templateName Tên tệp view (ví dụ: 'pages/home')
     * @param array $data Dữ liệu truyền vào view
     * @return string HTML đã được render
     */
    public function render(string $templateName, array $data = []): string;
}