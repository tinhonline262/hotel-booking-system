<?php

namespace App\Infrastructure\ThirdPartyIntegrations;

use App\Domain\Exceptions\StorageException;
use App\Domain\Interfaces\Services\ImageStorageInterface;
use App\Domain\ValueObjects\UploadedFile;

class CloudinaryImageStorage implements ImageStorageInterface
{
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;

        if (empty($config['cloud_name']) || empty($config['api_key']) || empty($config['api_secret'])) {
            throw new StorageException("Cloudinary configuration is incomplete");
        }
    }

    public function store(UploadedFile $file, string $directory): array
    {
        // Simple implementation using Cloudinary Upload API
        $cloudName = $this->config['cloud_name'];
        $apiKey = $this->config['api_key'];
        $apiSecret = $this->config['api_secret'];

        $timestamp = time();
        $publicId = $directory . '/' . 'img_' . uniqid() . '_' . $timestamp;

        // Generate signature
        $paramsToSign = "public_id={$publicId}&timestamp={$timestamp}";
        $signature = sha1($paramsToSign . $apiSecret);

        // Prepare upload data
        $uploadData = [
            'file' => new \CURLFile($file->getTempPath(), $file->getMimeType(), $file->getOriginalName()),
            'public_id' => $publicId,
            'timestamp' => $timestamp,
            'api_key' => $apiKey,
            'signature' => $signature,
        ];

        // Upload to Cloudinary
        $ch = curl_init("https://api.cloudinary.com/v1_1/{$cloudName}/image/upload");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $uploadData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new StorageException("Cloudinary upload failed: " . $response);
        }

        $result = json_decode($response, true);

        if (!isset($result['secure_url'])) {
            throw new StorageException("Invalid Cloudinary response");
        }

        return [
            'path' => $result['secure_url'],
            'url' => $result['secure_url'],
            'storage_type' => 'cloudinary',
            'cloudinary_public_id' => $result['public_id'],
            'file_size' => $result['bytes'] ?? $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ];
    }

    public function delete(string $path, ?string $publicId = null): bool
    {
        if (!$publicId) {
            return false;
        }

        $cloudName = $this->config['cloud_name'];
        $apiKey = $this->config['api_key'];
        $apiSecret = $this->config['api_secret'];

        $timestamp = time();
        $paramsToSign = "public_id={$publicId}&timestamp={$timestamp}";
        $signature = sha1($paramsToSign . $apiSecret);

        $deleteData = [
            'public_id' => $publicId,
            'timestamp' => $timestamp,
            'api_key' => $apiKey,
            'signature' => $signature,
        ];

        $ch = curl_init("https://api.cloudinary.com/v1_1/{$cloudName}/image/destroy");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($deleteData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode === 200;
    }

    public function exists(string $path): bool
    {
        // For Cloudinary, we assume the image exists if we have the URL
        // A proper implementation would use the Admin API to check
        return !empty($path);
    }

    public function getUrl(string $path): string
    {
        return $path; // Cloudinary stores full URL
    }

    public function getStorageType(): string
    {
        return 'cloudinary';
    }

    public function healthCheck(): array
    {
        try {
            $cloudName = $this->config['cloud_name'];
            $apiKey = $this->config['api_key'];
            $apiSecret = $this->config['api_secret'];

            // Ping Cloudinary API
            $ch = curl_init("https://api.cloudinary.com/v1_1/{$cloudName}/resources/image");
            curl_setopt($ch, CURLOPT_USERPWD, "{$apiKey}:{$apiSecret}");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            return [
                'storage_type' => 'cloudinary',
                'status' => $httpCode === 200 ? 'healthy' : 'error',
                'configured' => true,
                'cloud_name' => $cloudName,
            ];
        } catch (\Exception $e) {
            return [
                'storage_type' => 'cloudinary',
                'status' => 'error',
                'error' => $e->getMessage(),
            ];
        }
    }

    public function getStorageInfo(): array
    {
        try {
            $cloudName = $this->config['cloud_name'];
            $apiKey = $this->config['api_key'];
            $apiSecret = $this->config['api_secret'];

            // Get usage from Cloudinary
            $ch = curl_init("https://api.cloudinary.com/v1_1/{$cloudName}/usage");
            curl_setopt($ch, CURLOPT_USERPWD, "{$apiKey}:{$apiSecret}");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            curl_close($ch);

            $usage = json_decode($response, true);

            return [
                'storage_type' => 'cloudinary',
                'current_usage' => $usage['storage']['usage'] ?? 0,
                'storage_limit' => $usage['storage']['limit'] ?? 0,
                'available_space' => ($usage['storage']['limit'] ?? 0) - ($usage['storage']['usage'] ?? 0),
                'usage_percentage' => ($usage['storage']['usage'] ?? 0) / max(($usage['storage']['limit'] ?? 1), 1) * 100,
                'cloud_name' => $cloudName,
            ];
        } catch (\Exception $e) {
            return [
                'storage_type' => 'cloudinary',
                'error' => $e->getMessage(),
            ];
        }
    }
}

