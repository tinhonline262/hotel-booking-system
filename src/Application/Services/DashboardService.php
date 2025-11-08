<?php

namespace App\Application\Services;

use App\Application\DTOs\DashboardStatsDTO;
use App\Application\Interfaces\DashboardServiceInterface;
use App\Domain\Interfaces\Repositories\RoomRepositoryInterface;
use App\Domain\Interfaces\Repositories\RoomTypeRepositoryInterface;

class DashboardService implements DashboardServiceInterface
{
    private RoomRepositoryInterface $roomRepository;
    private RoomTypeRepositoryInterface $roomTypeRepository;

    public function __construct(
        RoomRepositoryInterface $roomRepository,
        RoomTypeRepositoryInterface $roomTypeRepository
    ) {
        $this->roomRepository = $roomRepository;
        $this->roomTypeRepository = $roomTypeRepository;
    }

    public function getStats(): DashboardStatsDTO
    {
        // Get all rooms for counting
        $allRooms = $this->roomRepository->findAll();
        
        // Count rooms by status
        $availableRooms = $occupiedRooms = $cleaningRooms = $maintenanceRooms = 0;
        foreach ($allRooms as $room) {
            switch ($room->getStatus()) {
                case 'available':
                    $availableRooms++;
                    break;
                case 'occupied':
                    $occupiedRooms++;
                    break;
                case 'cleaning':
                    $cleaningRooms++;
                    break;
                case 'maintenance':
                    $maintenanceRooms++;
                    break;
            }
        }

        // Calculate total rooms
        $totalRooms = count($allRooms);

        // Get room types
        $roomTypes = $this->roomTypeRepository->findAll();
        $totalRoomTypes = count($roomTypes);

        // Calculate occupancy rate
        $occupancyRate = $totalRooms > 0 ? ($occupiedRooms / $totalRooms) * 100 : 0;

        // Calculate room type distribution
        $roomTypeDistribution = [];
        foreach ($roomTypes as $type) {
            $roomCount = 0;
            foreach ($allRooms as $room) {
                if ($room->getRoomTypeId() === $type->getId()) {
                    $roomCount++;
                }
            }
            $roomTypeDistribution[$type->getName()] = [
                'count' => $roomCount,
                'percentage' => $totalRooms > 0 ? ($roomCount / $totalRooms) * 100 : 0
            ];
        }

        return new DashboardStatsDTO(
            $totalRooms,
            $availableRooms,
            $occupiedRooms,
            $cleaningRooms,
            $maintenanceRooms,
            $totalRoomTypes,
            $occupancyRate,
            $roomTypeDistribution
        );
    }
}