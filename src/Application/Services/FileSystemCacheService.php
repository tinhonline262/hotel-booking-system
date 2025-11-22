<?php
namespace App\Application\Services;

use App\Application\Interfaces\ICacheService;

class FileSystemCacheService implements ICacheService
{
    private string $cachePath;

    public function __construct(string $cachePath)
    {
        // Đảm bảo đường dẫn không có dấu gạch chéo thừa ở cuối
        $this->cachePath = rtrim($cachePath, '/\\');

        // Tự động tạo thư mục cache nếu chưa có
        if (!is_dir($this->cachePath)) {
            mkdir($this->cachePath, 0755, true);
        }
    }

    public function get(string $key)
    {
        $filename = $this->getFilename($key);

        if (!file_exists($filename)) {
            return null;
        }

        // Đọc nội dung file
        $content = file_get_contents($filename);
        $data = unserialize($content);

        // Kiểm tra hết hạn
        if ($data['expire'] < time()) {
            unlink($filename); // Xóa file hết hạn
            return null;
        }

        return $data['value'];
    }

    public function set(string $key, $value, int $ttl = 3600): void
    {
        $data = [
            'value' => $value,
            'expire' => time() + $ttl
        ];

        // Lưu data đã serialize vào file
        file_put_contents($this->getFilename($key), serialize($data));
    }

    public function delete(string $key): void
    {
        $filename = $this->getFilename($key);
        if (file_exists($filename)) {
            unlink($filename);
        }
    }

    public function clear(): void
    {
        // Xóa tất cả file có đuôi .cache
        $files = glob($this->cachePath . '/*.cache');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    /**
     * Tạo tên file cache dựa trên key (mã hóa MD5 để an toàn)
     */
    private function getFilename(string $key): string
    {
        return $this->cachePath . '/' . md5($key) . '.cache';
    }
}