<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\Repositories\RoomTypeRepositoryInterface;

class FilterRoomTypesByPriceRangeUseCase
{
    private RoomTypeRepositoryInterface $repository;

    public function __construct(RoomTypeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(float $minPrice, float $maxPrice): array
    {
        if ($minPrice < 0 || $maxPrice < 0) {
            throw new \InvalidArgumentException("Prices cannot be negative");
        }

        if ($minPrice > $maxPrice) {
            throw new \InvalidArgumentException("Minimum price cannot be greater than maximum price");
        }

        return $this->repository->findByPriceRange($minPrice, $maxPrice);
    }
}

