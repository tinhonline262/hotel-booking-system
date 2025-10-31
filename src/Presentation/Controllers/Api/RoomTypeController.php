<?php

namespace App\Presentation\Controllers\Api;

use App\Application\DTOs\RoomTypeDTO;
use App\Application\Services\RoomTypeService;
use App\Domain\Exceptions\RoomTypeNotFoundException;
use App\Domain\Exceptions\InvalidRoomTypeDataException;

/**
 * Room Type REST API Controller
 * Clean Architecture - Presentation Layer
 */
class RoomTypeController extends BaseRestController
{
    private RoomTypeService $service;

    public function __construct(RoomTypeService $service)
    {
        $this->service = $service;
    }

    /**
     * GET /api/room-types
     * Get all room types
     */
    public function index(): void
    {
        try {
            $roomTypes = $this->service->getAllRoomTypes();

            $this->success(
                array_map(fn($rt) => $rt->toArray(), $roomTypes),
                'Room types retrieved successfully'
            );
        } catch (\Exception $e) {
            $this->serverError('Failed to retrieve room types: ' . $e->getMessage());
        }
    }

    /**
     * GET /api/room-types/{id}
     * Get single room type by ID
     */
    public function show(int $id): void
    {
        try {
            $roomType = $this->service->getRoomType($id);

            $this->success(
                $roomType->toArray(),
                'Room type retrieved successfully'
            );
        } catch (RoomTypeNotFoundException $e) {
            $this->notFound($e->getMessage());
        } catch (\Exception $e) {
            $this->serverError('Failed to retrieve room type: ' . $e->getMessage());
        }
    }

    /**
     * POST /api/room-types
     * Create new room type
     */
    public function create(): void
    {
        try {
            $data = $this->getJsonInput();

            // Handle amenities conversion
            if (isset($data['amenities']) && is_string($data['amenities'])) {
                $data['amenities'] = array_map('trim', explode(',', $data['amenities']));
            }

            $dto = RoomTypeDTO::fromArray($data);
            $result = $this->service->createRoomType($dto);

            $this->created(
                ['success' => $result],
                'Room type created successfully'
            );
        } catch (InvalidRoomTypeDataException $e) {
            $this->validationError($e->getErrors(), 'Validation failed');
        } catch (\Exception $e) {
            $this->serverError('Failed to create room type: ' . $e->getMessage());
        }
    }

    /**
     * PUT /api/room-types/{id}
     * Update existing room type
     */
    public function update(int $id): void
    {
        try {
            $data = $this->getJsonInput();

            // Handle amenities conversion
            if (isset($data['amenities']) && is_string($data['amenities'])) {
                $data['amenities'] = array_map('trim', explode(',', $data['amenities']));
            }

            $dto = RoomTypeDTO::fromArray($data);
            $result = $this->service->updateRoomType($id, $dto);

            $this->success(
                ['success' => $result],
                'Room type updated successfully'
            );
        } catch (RoomTypeNotFoundException $e) {
            $this->notFound($e->getMessage());
        } catch (InvalidRoomTypeDataException $e) {
            $this->validationError($e->getErrors(), 'Validation failed');
        } catch (\Exception $e) {
            $this->serverError('Failed to update room type: ' . $e->getMessage());
        }
    }

    /**
     * DELETE /api/room-types/{id}
     * Delete room type
     */
    public function delete(int $id): void
    {
        try {
            $result = $this->service->deleteRoomType($id);

            $this->success(
                ['success' => $result],
                'Room type deleted successfully'
            );
        } catch (RoomTypeNotFoundException $e) {
            $this->notFound($e->getMessage());
        } catch (\Exception $e) {
            $this->serverError('Failed to delete room type: ' . $e->getMessage());
        }
    }

    /**
     * GET /api/room-types/filter/capacity?capacity=2
     * Filter room types by capacity
     */
    public function filterByCapacity(): void
    {
        try {
            $params = $this->getQueryParams();
            $capacity = (int)($params['capacity'] ?? 1);

            if ($capacity < 1) {
                $this->error('Capacity must be at least 1', 400);
            }

            $roomTypes = $this->service->filterByCapacity($capacity);

            $this->success(
                array_map(fn($rt) => $rt->toArray(), $roomTypes),
                'Room types filtered by capacity'
            );
        } catch (\Exception $e) {
            $this->serverError('Failed to filter room types: ' . $e->getMessage());
        }
    }

    /**
     * GET /api/room-types/filter/price?min=100&max=500
     * Filter room types by price range
     */
    public function filterByPriceRange(): void
    {
        try {
            $params = $this->getQueryParams();
            $minPrice = (float)($params['min'] ?? 0);
            $maxPrice = (float)($params['max'] ?? PHP_FLOAT_MAX);

            if ($minPrice < 0 || $maxPrice < 0) {
                $this->error('Price values must be non-negative', 400);
            }

            if ($minPrice > $maxPrice) {
                $this->error('Minimum price cannot exceed maximum price', 400);
            }

            $roomTypes = $this->service->filterByPriceRange($minPrice, $maxPrice);

            $this->success(
                array_map(fn($rt) => $rt->toArray(), $roomTypes),
                'Room types filtered by price range'
            );
        } catch (\Exception $e) {
            $this->serverError('Failed to filter room types: ' . $e->getMessage());
        }
    }
}

