<?php

namespace App\Application\Services;

use App\Application\DTOs\RoomTypeDTO;
use App\Application\Interfaces\RoomTypeServiceInterface;
use App\Application\UseCases\CreateRoomTypeUseCase;
use App\Application\UseCases\GetRoomTypeUseCase;
use App\Application\UseCases\GetAllRoomTypesUseCase;
use App\Application\UseCases\UpdateRoomTypeUseCase;
use App\Application\UseCases\DeleteRoomTypeUseCase;
use App\Application\UseCases\FilterRoomTypesByCapacityUseCase;
use App\Application\UseCases\FilterRoomTypesByPriceRangeUseCase;
use App\Domain\Entities\RoomType;
use App\Domain\Exceptions\InvalidRoomTypeDataException;
use App\Domain\Exceptions\RoomTypeNotFoundException;

class RoomTypeService implements RoomTypeServiceInterface
{
    private CreateRoomTypeUseCase $createUseCase;
    private GetRoomTypeUseCase $getUseCase;
    private GetAllRoomTypesUseCase $getAllUseCase;
    private UpdateRoomTypeUseCase $updateUseCase;
    private DeleteRoomTypeUseCase $deleteUseCase;
    private FilterRoomTypesByCapacityUseCase $filterByCapacityUseCase;
    private FilterRoomTypesByPriceRangeUseCase $filterByPriceRangeUseCase;

    public function __construct(
        CreateRoomTypeUseCase $createUseCase,
        GetRoomTypeUseCase $getUseCase,
        GetAllRoomTypesUseCase $getAllUseCase,
        UpdateRoomTypeUseCase $updateUseCase,
        DeleteRoomTypeUseCase $deleteUseCase,
        FilterRoomTypesByCapacityUseCase $filterByCapacityUseCase,
        FilterRoomTypesByPriceRangeUseCase $filterByPriceRangeUseCase
    ) {
        $this->createUseCase = $createUseCase;
        $this->getUseCase = $getUseCase;
        $this->getAllUseCase = $getAllUseCase;
        $this->updateUseCase = $updateUseCase;
        $this->deleteUseCase = $deleteUseCase;
        $this->filterByCapacityUseCase = $filterByCapacityUseCase;
        $this->filterByPriceRangeUseCase = $filterByPriceRangeUseCase;
    }

    /**
     * @throws InvalidRoomTypeDataException
     */
    public function createRoomType(RoomTypeDTO $dto): bool
    {
        return $this->createUseCase->execute($dto);
    }

    /**
     * @throws RoomTypeNotFoundException
     */
    public function getRoomType(int $id): RoomType
    {
        return $this->getUseCase->execute($id);
    }

    public function getAllRoomTypes(): array
    {
        return $this->getAllUseCase->execute();
    }

    /**
     * @throws RoomTypeNotFoundException
     * @throws InvalidRoomTypeDataException
     */
    public function updateRoomType(int $id, RoomTypeDTO $dto): bool
    {
        return $this->updateUseCase->execute($id, $dto);
    }

    /**
     * @throws RoomTypeNotFoundException
     */
    public function deleteRoomType(int $id): bool
    {
        return $this->deleteUseCase->execute($id);
    }

    public function filterByCapacity(int $minCapacity): array
    {
        return $this->filterByCapacityUseCase->execute($minCapacity);
    }

    public function filterByPriceRange(float $minPrice, float $maxPrice): array
    {
        return $this->filterByPriceRangeUseCase->execute($minPrice, $maxPrice);
    }
}

