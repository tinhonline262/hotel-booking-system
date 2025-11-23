<?php

namespace App\Presentation\Controllers\Api;
use App\Application\Interfaces\RoomServiceInterface;
use App\Domain\Exceptions\RoomNotFoundException;
use App\Application\Services\RoomTypeService;
use App\Domain\Exceptions\RoomTypeNotFoundException;
class RoomDetailController extends BaseRestController
{
    private RoomServiceInterface $roomService;
    private RoomTypeService $roomTypeService;

    public function __construct(RoomServiceInterface $roomService, RoomTypeService $roomTypeService){
        parent::__construct();
        $this->roomService = $roomService;
        $this->roomTypeService = $roomTypeService;
    }
    /**
     * GET /api/room-details/{id}
     * Get single room by ID
     */
    public function getDetailRooms(int $roomId):void{
        try{
            $room = $this->roomService->GetRoom($roomId);
            $roomType = $this->roomTypeService->GetRoomType($room->getRoomTypeId());
            $this->success(
                [
                    'room' => $room->toArray(),
                    'roomType' => $roomType->toArray(),
                ],
                "Room retrieved successfully"
            );
        } catch (RoomTypeNotFoundException $e) {
            $this->notFound($e->getMessage());
        } catch (\Exception $e) {
            $this->serverError('Failed to retrieve room type: ' . $e->getMessage());
        }
    }
}