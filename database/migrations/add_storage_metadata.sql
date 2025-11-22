-- Migration: Add storage metadata columns to room_images table
-- Run this SQL to update existing room_images table

-- Add new columns if they don't exist
ALTER TABLE room_images
ADD COLUMN IF NOT EXISTS storage_type ENUM('local', 'cloudinary') DEFAULT 'local' AFTER image_url,
ADD COLUMN IF NOT EXISTS cloudinary_public_id VARCHAR(255) NULL AFTER storage_type,
ADD COLUMN IF NOT EXISTS file_size BIGINT NOT NULL DEFAULT 0 COMMENT 'Size in bytes' AFTER cloudinary_public_id,
ADD COLUMN IF NOT EXISTS mime_type VARCHAR(100) NOT NULL DEFAULT 'image/jpeg' AFTER file_size,
ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at,
ADD INDEX IF NOT EXISTS idx_is_primary (is_primary);

-- Create storage_settings table
CREATE TABLE IF NOT EXISTS storage_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default storage settings
INSERT INTO storage_settings (setting_key, setting_value) VALUES
('default_storage_provider', 'local'),
('max_file_size', '5242880'), -- 5MB in bytes
('allowed_mime_types', 'image/jpeg,image/png,image/jpg,image/webp'),
('local_storage_path', 'public/uploads/rooms'),
('local_storage_limit', '1073741824'), -- 1GB in bytes
('cloudinary_cloud_name', ''),
('cloudinary_api_key', ''),
('cloudinary_api_secret', ''),
('cloudinary_folder', 'hotel/rooms')
ON DUPLICATE KEY UPDATE setting_key=setting_key;

