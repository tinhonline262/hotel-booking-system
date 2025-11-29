<?php
namespace App\Application\Interfaces;

interface ICacheService
{
    /**
     * Lấy dữ liệu từ cache
     * @param string $key Khóa định danh
     * @return mixed|null Trả về dữ liệu hoặc null nếu không có/hết hạn
     */
    public function get(string $key);

    /**
     * Lưu dữ liệu vào cache
     * @param string $key Khóa định danh
     * @param mixed $value Dữ liệu cần lưu
     * @param int $ttl Thời gian sống (giây). Mặc định 3600s (1 giờ)
     */
    public function set(string $key, $value, int $ttl = 3600): void;

    /**
     * Xóa một item khỏi cache
     */
    public function delete(string $key): void;

    /**
     * Xóa toàn bộ cache
     */
    public function clear(): void;
}