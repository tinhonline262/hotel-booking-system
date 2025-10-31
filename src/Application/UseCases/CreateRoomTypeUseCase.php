<?php

namespace App\Application\UseCases;

use App\Application\DTOs\RoomTypeDTO;
use App\Application\Validators\RoomTypeValidator;
use App\Domain\Entities\RoomType;
use App\Domain\Exceptions\InvalidRoomTypeDataException;
use App\Domain\Interfaces\Repositories\RoomTypeRepositoryInterface;

class CreateRoomTypeUseCase
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

    public function execute(RoomTypeDTO $dto): bool
    {
        // Validate input
        $errors = $this->validator->validate($dto->toArray());
        if (!empty($errors)) {
            throw new InvalidRoomTypeDataException($errors);
        }

        // Create entity
        $roomType = new RoomType(
            null,
            $dto->name,
            $dto->description,
            $dto->capacity,
            $dto->pricePerNight,
            $dto->amenities
        );

        // Save to repository
        return $this->repository->save($roomType);
    }
}

