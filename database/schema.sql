-- 1. Room Types Table
CREATE TABLE `room_types` (
                              `id` INT NOT NULL AUTO_INCREMENT,
                              `name` VARCHAR(100) NOT NULL,
                              `description` TEXT,
                              `capacity` INT NOT NULL,
                              `price_per_night` DECIMAL(10, 2) NOT NULL,
                              `amenities` TEXT,
                              `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                              `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                              PRIMARY KEY (`id`),
                              INDEX `idx_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Rooms Table
CREATE TABLE rooms (
                       id INT PRIMARY KEY AUTO_INCREMENT,
                       room_number VARCHAR(20) UNIQUE NOT NULL,
                       room_type_id INT NOT NULL,
                       status ENUM('available', 'occupied', 'cleaning') DEFAULT 'available',
                       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                       updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                       FOREIGN KEY (room_type_id) REFERENCES room_types(id) ON DELETE RESTRICT,
                       INDEX idx_room_number (room_number),
                       INDEX idx_status (status),
                       INDEX idx_room_type (room_type_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Room Images Table (for multiple images per room)
CREATE TABLE room_images (
                             id INT PRIMARY KEY AUTO_INCREMENT,
                             room_id INT NOT NULL,
                             image_url VARCHAR(500) NOT NULL,
                             storage_type ENUM('local', 'cloud') DEFAULT 'local',
                             cloudinary_public_id VARCHAR(255),
                             file_size INT,
                             mime_type VARCHAR(100),
                             display_order INT DEFAULT 0,
                             is_primary BOOLEAN DEFAULT FALSE,
                             alt_text VARCHAR(255),
                             created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                             FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
                             INDEX idx_room_id (room_id),
                             INDEX idx_display_order (display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Bookings Table
CREATE TABLE bookings (
                          id INT PRIMARY KEY AUTO_INCREMENT,
                          booking_code VARCHAR(20) UNIQUE NOT NULL,
                          room_id INT NOT NULL,
                          customer_name VARCHAR(100) NOT NULL,
                          customer_email VARCHAR(100) NOT NULL,
                          customer_phone VARCHAR(20) NOT NULL,
                          check_in_date DATE NOT NULL,
                          check_out_date DATE NOT NULL,
                          num_guests INT NOT NULL,
                          total_price DECIMAL(10, 2) NOT NULL,
                          status ENUM('pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled') DEFAULT 'pending',
                          special_requests TEXT,
                          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                          updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                          FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE RESTRICT,
                          INDEX idx_booking_code (booking_code),
                          INDEX idx_room_id (room_id),
                          INDEX idx_check_in (check_in_date),
                          INDEX idx_check_out (check_out_date),
                          INDEX idx_status (status),
                          INDEX idx_customer_email (customer_email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Admins Table
CREATE TABLE admins (
                        id INT PRIMARY KEY AUTO_INCREMENT,
                        username VARCHAR(50) UNIQUE NOT NULL,
                        password VARCHAR(255) NOT NULL,
                        full_name VARCHAR(100) NOT NULL,
                        email VARCHAR(100) NOT NULL,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                        INDEX idx_username (username),
                        INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Storage Settings Table (for image storage configuration)
CREATE TABLE storage_settings (
                                   id INT PRIMARY KEY AUTO_INCREMENT,
                                   setting_key VARCHAR(100) UNIQUE NOT NULL,
                                   setting_value TEXT NOT NULL,
                                   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                   updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                                   INDEX idx_setting_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample Data Insertion

-- Insert Room Types
INSERT INTO room_types (name, description, capacity, price_per_night, amenities) VALUES
                                                                                     ('Standard', 'Comfortable room with basic amenities', 2, 500000, 'WiFi, Air Conditioning, TV, Mini Fridge'),
                                                                                     ('Deluxe', 'Spacious room with city view and premium amenities', 2, 800000, 'WiFi, Air Conditioning, TV, Mini Bar, Bathtub, City View'),
                                                                                     ('Suite', 'Luxury suite with separate living area', 4, 1500000, 'WiFi, Air Conditioning, Smart TV, Mini Bar, Jacuzzi, Ocean View, Living Room');

-- Insert Rooms
INSERT INTO rooms (room_number, room_type_id, status) VALUES
                                                          ('R-0101', 1, 'available'),
                                                          ('R-0102', 1, 'available'),
                                                          ('R-0201', 2, 'available'),
                                                          ('R-0202', 2, 'occupied'),
                                                          ('R-0301', 3, 'available'),
                                                          ('R-0302', 3, 'cleaning');

-- Insert Room Images
INSERT INTO room_images (room_id, image_url, storage_type, cloudinary_public_id, file_size, mime_type, display_order, is_primary, alt_text) VALUES
-- Images for Room R-0101
(1, '/uploads/rooms/r0101-1.jpg', 'local', NULL, 204800, 'image/jpeg', 1, TRUE, 'Room R-0101 bed view'),
(1, '/uploads/rooms/r0101-2.jpg', 'local', NULL, 153600, 'image/jpeg', 2, FALSE, 'Room R-0101 bathroom'),
-- Images for Room R-0102
(2, '/uploads/rooms/r0102-1.jpg', 'local', NULL, 204800, 'image/jpeg', 1, TRUE, 'Room R-0102 bed view'),
(2, '/uploads/rooms/r0102-2.jpg', 'local', NULL, 153600, 'image/jpeg', 2, FALSE, 'Room R-0102 bathroom'),
-- Images for Room R-0201
(3, '/uploads/rooms/r0201-1.jpg', 'local', NULL, 307200, 'image/jpeg', 1, TRUE, 'Room R-0201 bed view'),
(3, '/uploads/rooms/r0201-2.jpg', 'local', NULL, 204800, 'image/jpeg', 2, FALSE, 'Room R-0201 city view'),
(3, '/uploads/rooms/r0201-3.jpg', 'local', NULL, 153600, 'image/jpeg', 3, FALSE, 'Room R-0201 bathroom'),
-- Images for Room R-0202
(4, '/uploads/rooms/r0202-1.jpg', 'local', NULL, 307200, 'image/jpeg', 1, TRUE, 'Room R-0202 bed view'),
(4, '/uploads/rooms/r0202-2.jpg', 'local', NULL, 204800, 'image/jpeg', 2, FALSE, 'Room R-0202 city view'),
-- Images for Room R-0301
(5, '/uploads/rooms/r0301-1.jpg', 'local', NULL, 409600, 'image/jpeg', 1, TRUE, 'Suite R-0301 living room'),
(5, '/uploads/rooms/r0301-2.jpg', 'local', NULL, 307200, 'image/jpeg', 2, FALSE, 'Suite R-0301 bedroom'),
(5, '/uploads/rooms/r0301-3.jpg', 'local', NULL, 204800, 'image/jpeg', 3, FALSE, 'Suite R-0301 bathroom'),
(5, '/uploads/rooms/r0301-4.jpg', 'local', NULL, 307200, 'image/jpeg', 4, FALSE, 'Suite R-0301 ocean view'),
-- Images for Room R-0302
(6, '/uploads/rooms/r0302-1.jpg', 'local', NULL, 409600, 'image/jpeg', 1, TRUE, 'Suite R-0302 living room'),
(6, '/uploads/rooms/r0302-2.jpg', 'local', NULL, 307200, 'image/jpeg', 2, FALSE, 'Suite R-0302 bedroom');

-- Insert Sample Bookings
INSERT INTO bookings (booking_code, room_id, customer_name, customer_email, customer_phone, check_in_date, check_out_date, num_guests, total_price, status, special_requests) VALUES
                                                                                                                                                                                  ('BK-001', 4, 'Nguyen Van A', 'nguyenvana@email.com', '0901234567', '2025-10-28', '2025-10-30', 2, 1600000, 'checked_in', 'Late check-in requested'),
                                                                                                                                                                                  ('BK-002', 1, 'Tran Thi B', 'tranthib@email.com', '0912345678', '2025-11-01', '2025-11-03', 2, 1000000, 'confirmed', NULL),
                                                                                                                                                                                  ('BK-003', 5, 'Le Van C', 'levanc@email.com', '0923456789', '2025-11-05', '2025-11-08', 4, 4500000, 'pending', 'Need baby cot');

-- Insert Admin (password should be hashed in real application)
-- Example password: 'admin123' (should use bcrypt or similar in production)
INSERT INTO admins (username, password, full_name, email) VALUES
    ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin User', 'admin@hotel.com');

-- Insert Storage Settings (Default Configuration)
INSERT INTO storage_settings (setting_key, setting_value) VALUES
('default_storage_type', 'local'),
('max_file_size', '5242880'),
('allowed_mime_types', 'image/jpeg,image/png,image/jpg,image/webp'),
('local_storage_limit', '1073741824'),
('local_storage_path', 'public/uploads/rooms'),
('cloudinary_cloud_name', ''),
('cloudinary_api_key', ''),
('cloudinary_api_secret', '');
