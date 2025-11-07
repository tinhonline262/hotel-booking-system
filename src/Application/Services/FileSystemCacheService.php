<?php
namespace Hotel\Application\Services;

use Hotel\Application\Interfaces\ICacheService;

class FileSystemCacheService implements ICacheService
{
    private string $cacheDir;

    public function __construct(string $cacheDir)
    {
        $this->cacheDir = $cacheDir;
        if (!is_dir($this->cacheDir)) {
            @mkdir($this->cacheDir, 0775, true);
        }
    }

    private function getFilePath(string $key): string
    {
        return $this->cacheDir . '/' . sha1($key) . '.cache';
    }

    public function get(string $key): mixed
    {
        $file = $this->getFilePath($key);
        if (!file_exists($file) || !is_readable($file)) {
            return null;
        }

        $content = @file_get_contents($file);
        if ($content === false) {
            return null;
        }

        $data = @unserialize($content);
        if ($data === false) {
            @unlink($file); // Xóa file cache hỏng
            return null;
        }

        if (time() > $data['expires_at']) {
            @unlink($file); // Cache hết hạn
            return null;
        }

        return $data['value'];
    }

    public function set(string $key, mixed $value, int $ttl): void
    {
        if (!is_dir($this->cacheDir) || !is_writable($this->cacheDir)) {
            // Log lỗi nếu thư mục cache không thể ghi
            // error_log("Cache directory not writable: {$this->cacheDir}");
            return;
        }
        
        $file = $this->getFilePath($key);
        $data = [
            'expires_at' => time() + $ttl,
            'value' => $value,
        ];
        
        @file_put_contents($file, serialize($data), LOCK_EX);
    }
}