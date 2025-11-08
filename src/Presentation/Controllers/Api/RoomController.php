<?php

namespace App\Presentation\Controllers\Api;

use App\Application\DTOs\RoomDTO;
use App\Application\Interfaces\RoomServiceInterface;
use App\Domain\Exceptions\RoomNotFoundException;
use App\Domain\Exceptions\InvalidRoomDataExceptions;
class RoomController extends BaseRestController
{
    private RoomServiceInterface $service;

    public function __construct(RoomServiceInterface $service)
    {
        parent::__construct();
        $this->service = $service;
    }


    /**
     * GET /api/rooms
     * Get all rooms
     */
    public function index():void{
        try{
            $rooms = $this->service->getAllRooms();
            $this->success(
                array_map(fn($rt)=>$rt->toArray(),$rooms),
                "Rooms retrieved successfully"
            );
        }
        catch (\Exception $e){
            $this->serverError('Failed to retrieve rooms ' . $e->getMessage());
        }
    }

    /**
     * GET /api/room/{id}
     * Get single room by ID
     */
    public function show(int $id):void{
        try{
            $room = $this->service->getRoom($id);
            $this->success(
                $room->toArray(),
                "Room retrieved successfully"
            );
        } catch(RoomNotFoundException $e){
            $this->notFound($e->getMessage());
        } catch (\Exception $e){
            $this->serverError('Failed to retrieve room ' . $e->getMessage());
        }
    }

    /**
     * POST /api/room
     * Create new room
     */

    public function create(): void
    {
        try{
            $data = $this->getJsonInput();

            // Handle amenities conversion
            $dto = RoomDTO::fromArray($data);
            $result = $this->service->createRoom($dto);

            $this->created(
                ['success' => $result],
                'Room created successfully'
            );
        } catch (invalidRoomDataExceptions $e) {
            $this->validationError($e->getErrors(),'validate fail');
        } catch (\Exception $e){
            $this->serverError('Failed to create room ' . $e->getMessage());
        }
    }

    /**
     * PUT /api/room/{id}
     * Update existing room
     */
    public function update(int $id): void{
        try {
            $data = $this->getJsonInput();

            $dto = RoomDTO::fromArray($data);
            $result = $this->service->updateRoom($dto,$id);

            $this->success(
                ['success' => $result],
                'Room updated successfully'
            );
        } catch (RoomNotFoundException $e) {
            $this->notFound($e->getMessage());
        } catch (InvalidRoomDataExceptions $e) {
            $this->validationError($e->getErrors(), 'Validation failed');
        } catch (\Exception $e) {
            $this->serverError('Failed to update room type: ' . $e->getMessage());
        }
    }

    /**
     * DELETE /api/room/{id}
     * Delete room
     */
    public function delete(int $id): void{
        try {
            $result = $this->service->deleteRoom($id);

            $this->success(
                ['success' => $result],
                'Room type deleted successfully'
            );
        } catch (RoomNotFoundException $e) {
            $this->notFound($e->getMessage());
        } catch (\Exception $e) {
            $this->serverError('Failed to delete room type: ' . $e->getMessage());
        }
    }

    /**
     * GET /api/rooms/filter/status?status='available'
     * Filter rooms by status
     */
    public function filterByStatus(): void
    {
        try {
            $params = $this->getQueryParams();
            $status = ($params['status'] ?? 'available');

            $rooms = $this->service->FilterRoomByStatus($status);

            $this->success(
                array_map(fn($rt) => $rt->toArray(), $rooms),
                'Room filtered by status'
            );
        } catch (\Exception $e) {
            $this->serverError('Failed to filter rooms: ' . $e->getMessage());
        }
    }

    public function filterByRoomNumber(): void
    {
        try {
            $params = $this->getQueryParams();
            $number = ($params['room-number'] ?? 1);

            if ($number < 0) {
                $this->error('Minimum number is not negative', 400);
            }

            $room = $this->service->FilterRoomByRoomNumber($number);

            $this->success(
               $room->toArray(),
                'Room filtered by room number'
            );
        } catch (RoomNotFoundException $e) {
            $this->notFound($e->getMessage());
        } catch (\Exception $e) {
            $this->serverError('Failed to filter room: ' . $e->getMessage());
        }
    }


}