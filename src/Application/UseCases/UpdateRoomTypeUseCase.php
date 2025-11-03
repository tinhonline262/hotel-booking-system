<?php

namespace App\Application\UseCases;

use App\Application\DTOs\RoomTypeDTO;
use App\Application\Validators\RoomTypeValidator;
use App\Domain\Entities\RoomType;
use App\Domain\Exceptions\InvalidRoomTypeDataException;
use App\Domain\Exceptions\RoomTypeNotFoundException;
use App\Domain\Interfaces\Repositories\RoomTypeRepositoryInterface;

class UpdateRoomTypeUseCase
{
    private RoomTypeRepositoryInterface $repository;
    private RoomTypeValidator $validator;

    public function __construct(
        RoomTypeRepositoryInterface $repository,
        RoomTypeValidator $validator
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
    }

    /**
     * @throws RoomTypeNotFoundException
     * @throws InvalidRoomTypeDataException
     */
    public function execute(int $id, RoomTypeDTO $dto): bool
    {
        // Check if exists
        if (!$this->repository->exists($id)) {
            throw new RoomTypeNotFoundException($id);
        }

        // Validate input
        $errors = $this->validator->validateUpdate($dto->toArray(), $id);
        if (!empty($errors)) {
            throw new InvalidRoomTypeDataException($errors);
        }

        // Create entity with updated data
        $roomType = new RoomType(
            $id,
            $dto->name,
            $dto->description,
            $dto->capacity,
            $dto->pricePerNight,
            $dto->amenities
        );

        // Update in repository
        return $this->repository->update($roomType, $id);
    }
}

