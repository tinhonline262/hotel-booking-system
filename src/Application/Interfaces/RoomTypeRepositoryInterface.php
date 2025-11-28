<?php
namespace App\Domain\Interfaces;

use App\Domain\Entities\RoomType;

interface RoomTypeRepositoryInterface
{
    // ... các phương thức khác bạn đã có (getAll, findById, create...)
    
    /**
     * (THÊM MỚI)
     * Lấy danh sách các loại phòng nổi bật để hiển thị trên trang chủ
     */
    public function getFeaturedRoomTypes(int $limit): array;
}