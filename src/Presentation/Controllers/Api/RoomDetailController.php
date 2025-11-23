<?php

namespace App\Presentation\Controllers\Api;
use App\Application\Interfaces\RoomServiceInterface;
use App\Application\Services\RoomTypeService;
use App\Domain\Exceptions\RoomNotFoundException;
class RoomDetailController extends BaseRestController
{
    private RoomServiceInterface $roomService;

    public function __construct(RoomServiceInterface $roomService, RoomTypeService $roomTypeService){
        parent::__construct();
        $this->roomService = $roomService;
    }
    /**
     * GET /api/room-details/{id}
     * Get single room by ID
     */
    public function getDetailRooms(int $id):void{
        try{
            $room = $this->roomService->getRoomWithDetails($id);
            $this->success(
                $room,
                "Room with details retrieved successfully"
            );
        }catch(RoomNotFoundException $e){
            $this->notFound($e->getMessage());
        } catch (\Exception $e){
            $this->serverError('Failed to retrieve room with details: ' . $e->getMessage());
        }
    }
}