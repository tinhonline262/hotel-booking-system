<?php
namespace App\Application\UseCases;

use App\Domain\Interfaces\RoomTypeRepositoryInterface;

class GetFeaturedRoomTypesUseCase
{
    public function __construct(
        private RoomTypeRepositoryInterface $roomTypeRepo
    ) {}

    /**
     * Lấy ra 3 phòng nổi bật cho trang chủ
     */
    public function execute(int $limit = 3): array
    {
        return $this->roomTypeRepo->getFeaturedRoomTypes($limit);
    }
}