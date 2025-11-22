<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\Repositories\RoomImageRepositoryInterface;

class UpdateImageDisplayOrderUseCase
{
    private RoomImageRepositoryInterface $roomImageRepository;

    public function __construct(RoomImageRepositoryInterface $roomImageRepository)
    {
        $this->roomImageRepository = $roomImageRepository;
    }

    /**
     * Update display order for multiple images
     *
     * @param array $orders Array of ['image_id' => order_number]
     * @return bool
     */
    public function execute(array $orders): bool
    {
        return $this->roomImageRepository->updateDisplayOrders($orders);
    }
}

