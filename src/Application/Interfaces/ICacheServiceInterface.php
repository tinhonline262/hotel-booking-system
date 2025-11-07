<?php
namespace Hotel\Application\Interfaces;

interface ICacheService
{
    /**
     * Lấy giá trị từ cache.
     * @param string $key
     * @return mixed Trả về giá trị đã lưu, hoặc null nếu không tìm thấy.
     */
    public function get(string $key): mixed;

    /**
     * Lưu giá trị vào cache.
     * @param string $key
     * @param mixed $value
     * @param int $ttl Số giây cache tồn tại (Time-to-live)
     */
    public function set(string $key, mixed $value, int $ttl): void;
}