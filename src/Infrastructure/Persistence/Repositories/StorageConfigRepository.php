<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Core\Database\Database;
use App\Domain\Interfaces\Services\StorageConfigInterface;

class StorageConfigRepository implements StorageConfigInterface
{
    private Database $db;
    private array $cache = [];

    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->loadSettings();
    }

    private function loadSettings(): void
    {
        $stmt = $this->db->query("SELECT setting_key, setting_value FROM storage_settings");
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($results as $row) {
            $this->cache[$row['setting_key']] = $row['setting_value'];
        }
    }

    public function getSetting(string $key): ?string
    {
        return $this->cache[$key] ?? null;
    }

    public function setSetting(string $key, string $value): bool
    {
        $sql = "INSERT INTO storage_settings (setting_key, setting_value) 
                VALUES (:key, :value) 
                ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)";

        try {
            $this->db->query($sql, ['key' => $key, 'value' => $value]);
            $this->cache[$key] = $value;
            return true;
        } catch (\Exception $e) {
            // Log error for debugging
            error_log("setSetting failed: " . $e->getMessage());
            return false;
        }
    }

    public function getMaxFileSize(): int
    {
        return (int) ($this->getSetting('max_file_size') ?? 5242880); // 5MB default
    }

    public function getAllowedMimeTypes(): array
    {
        $types = $this->getSetting('allowed_mime_types') ?? 'image/jpeg,image/png,image/jpg,image/webp';
        return explode(',', $types);
    }

    public function getLocalStorageLimit(): int
    {
        return (int) ($this->getSetting('local_storage_limit') ?? 1073741824); // 1GB default
    }

    public function getDefaultStorageType(): string
    {
        return $this->getSetting('default_storage_type') ?? 'local';
    }

    public function getCloudinaryConfig(): array
    {
        return [
            'cloud_name' => $this->getSetting('cloudinary_cloud_name') ?? '',
            'api_key' => $this->getSetting('cloudinary_api_key') ?? '',
            'api_secret' => $this->getSetting('cloudinary_api_secret') ?? '',
        ];
    }
}
