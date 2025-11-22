-- Migration: Create Storage Settings Table
-- Created: 2025-11-16
-- Description: Add storage_settings table for managing image storage configuration

-- Create storage_settings table
CREATE TABLE IF NOT EXISTS storage_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_setting_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default storage configuration
INSERT INTO storage_settings (setting_key, setting_value) VALUES
('default_storage_type', 'local'),
('max_file_size', '5242880'),
('allowed_mime_types', 'image/jpeg,image/png,image/jpg,image/webp'),
('local_storage_limit', '1073741824'),
('local_storage_path', 'public/uploads/rooms'),
('cloudinary_cloud_name', ''),
('cloudinary_api_key', ''),
('cloudinary_api_secret', '')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);
