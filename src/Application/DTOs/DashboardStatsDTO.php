<?php

namespace App\Application\DTOs;

class DashboardStatsDTO
{
    public int $totalRooms;
    public int $availableRooms;
    public int $occupiedRooms;
    public int $cleaningRooms;
    public int $maintenanceRooms;
    public int $totalRoomTypes;
    public float $occupancyRate;
    public array $roomTypeDistribution;

    public function __construct(
        int $totalRooms,
        int $availableRooms,
        int $occupiedRooms,
        int $cleaningRooms,
        int $maintenanceRooms,
        int $totalRoomTypes,
        float $occupancyRate,
        array $roomTypeDistribution
    ) {
        $this->totalRooms = $totalRooms;
        $this->availableRooms = $availableRooms;
        $this->occupiedRooms = $occupiedRooms;
        $this->cleaningRooms = $cleaningRooms;
        $this->maintenanceRooms = $maintenanceRooms;
        $this->totalRoomTypes = $totalRoomTypes;
        $this->occupancyRate = $occupancyRate;
        $this->roomTypeDistribution = $roomTypeDistribution;
    }

    public function toArray(): array
    {
        return [
            'total_rooms' => $this->totalRooms,
            'available_rooms' => $this->availableRooms,
            'occupied_rooms' => $this->occupiedRooms,
            'cleaning_rooms' => $this->cleaningRooms,
            'maintenance_rooms' => $this->maintenanceRooms,
            'total_room_types' => $this->totalRoomTypes,
            'occupancy_rate' => $this->occupancyRate,
            'room_type_distribution' => $this->roomTypeDistribution
        ];
    }
}