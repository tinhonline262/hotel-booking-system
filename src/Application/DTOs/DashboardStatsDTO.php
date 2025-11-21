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
    public array $recentBookings;
    public array $todayCheckIns;
    public array $todayCheckOuts;
    public int $todayCheckInsCount;
    public int $pendingBookingsCount;

    public function __construct(
        int $totalRooms,
        int $availableRooms,
        int $occupiedRooms,
        int $cleaningRooms,
        int $maintenanceRooms,
        int $totalRoomTypes,
        float $occupancyRate,
        array $roomTypeDistribution,
        array $recentBookings = [],
        array $todayCheckIns = [],
        array $todayCheckOuts = [],
        int $todayCheckInsCount = 0,
        int $pendingBookingsCount = 0
    ) {
        $this->totalRooms = $totalRooms;
        $this->availableRooms = $availableRooms;
        $this->occupiedRooms = $occupiedRooms;
        $this->cleaningRooms = $cleaningRooms;
        $this->maintenanceRooms = $maintenanceRooms;
        $this->totalRoomTypes = $totalRoomTypes;
        $this->occupancyRate = $occupancyRate;
        $this->roomTypeDistribution = $roomTypeDistribution;
        $this->recentBookings = $recentBookings;
        $this->todayCheckIns = $todayCheckIns;
        $this->todayCheckOuts = $todayCheckOuts;
        $this->todayCheckInsCount = $todayCheckInsCount;
        $this->pendingBookingsCount = $pendingBookingsCount;
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
            'room_type_distribution' => $this->roomTypeDistribution,
            'recent_bookings' => $this->recentBookings,
            'today_check_ins' => $this->todayCheckIns,
            'today_check_outs' => $this->todayCheckOuts,
            'today_check_ins_count' => $this->todayCheckInsCount,
            'pending_bookings_count' => $this->pendingBookingsCount
        ];
    }
}