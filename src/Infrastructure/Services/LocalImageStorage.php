<?php

namespace App\Infrastructure\Services;

use App\Domain\Interfaces\Services\ImageStorageInterface;
use App\Domain\ValueObjects\UploadedFile;
use App\Domain\Exceptions\StorageException;

class LocalImageStorage implements ImageStorageInterface
{
    private string $basePath;
    private string $baseUrl;
    private int $storageLimit;

    public function __construct(string $basePath, string $baseUrl, int $storageLimit)
    {
        $this->basePath = rtrim($basePath, '/\\');
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->storageLimit = $storageLimit;

        // Create directory if not exists
        if (!is_dir($this->basePath)) {
            if (!mkdir($this->basePath, 0755, true)) {
                throw new StorageException("Failed to create storage directory: {$this->basePath}");
            }
        }
    }

    /**
     * @throws StorageException
     */
    public function store(UploadedFile $file, string $directory): array
    {
        $targetDir = $this->baseUrl . DIRECTORY_SEPARATOR . trim($directory, '/\\');
        
        // Create subdirectory if not exists
        if (!is_dir($targetDir)) {
            if (!mkdir($targetDir, 0755, true)) {
                throw new StorageException("Failed to create directory: {$targetDir}");
            }
        }

        // Check storage limit
        $currentUsage = $this->getCurrentUsage();
        if ($currentUsage + $file->getSize() > $this->storageLimit) {
            throw new StorageException("Storage limit exceeded");
        }

        // Generate unique filename
        $extension = pathinfo($file->getOriginalName(), PATHINFO_EXTENSION);
        $filename = 'img_' . uniqid() . '_' . time() . '.' . $extension;
        $filePath = $targetDir . DIRECTORY_SEPARATOR . $filename;
        $filePath = str_replace('\\', '/', $filePath);
        // Move uploaded file
        if (!move_uploaded_file($file->getTempPath(), $filePath)) {
            throw new StorageException("Failed to move uploaded file");
        }

        // Generate relative path for URL
        $relativePath = str_replace($this->basePath . '/', '', $filePath);
        $url = '/'.$relativePath;

        return [
            'path' => $relativePath,
            'url' => $url,
            'storage_type' => 'local',
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ];
    }

    public function delete(string $path, ?string $publicId = null): bool
    {
        $fullPath = $this->basePath . DIRECTORY_SEPARATOR . ltrim($path, '/\\');
        
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        
        return false;
    }

    public function exists(string $path): bool
    {
        $fullPath = $this->basePath . DIRECTORY_SEPARATOR . ltrim($path, '/\\');
        return file_exists($fullPath);
    }

    public function getUrl(string $path): string
    {
        return $this->baseUrl . '/' . ltrim($path, '/');
    }

    public function getStorageType(): string
    {
        return 'local';
    }

    public function healthCheck(): array
    {
        $isWritable = is_writable($this->basePath);
        $diskSpace = disk_free_space($this->basePath);
        
        return [
            'storage_type' => 'local',
            'status' => $isWritable ? 'healthy' : 'error',
            'writable' => $isWritable,
            'disk_free_space' => $diskSpace,
            'base_path' => $this->basePath,
        ];
    }

    public function getStorageInfo(): array
    {
        $currentUsage = $this->getCurrentUsage();
        $freeSpace = disk_free_space($this->basePath);
        
        return [
            'storage_type' => 'local',
            'current_usage' => $currentUsage,
            'storage_limit' => $this->storageLimit,
            'available_space' => min($this->storageLimit - $currentUsage, $freeSpace),
            'usage_percentage' => ($currentUsage / $this->storageLimit) * 100,
            'base_path' => $this->basePath,
        ];
    }

    private function getCurrentUsage(): int
    {
        if (!is_dir($this->basePath)) {
            return 0;
        }

        $usage = 0;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->basePath, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $usage += $file->getSize();
            }
        }

        return $usage;
    }
}

