<?php

namespace App\Application\Services;

use App\Application\DTOs\DashboardStatsDTO;
use App\Application\Interfaces\DashboardServiceInterface;
use App\Domain\Interfaces\Repositories\RoomRepositoryInterface;
use App\Domain\Interfaces\Repositories\RoomTypeRepositoryInterface;
use App\Domain\Interfaces\Repositories\BookingRepositoryInterface;

class DashboardService implements DashboardServiceInterface
{
    private RoomRepositoryInterface $roomRepository;
    private RoomTypeRepositoryInterface $roomTypeRepository;
    private BookingRepositoryInterface $bookingRepository;

    public function __construct(
        RoomRepositoryInterface $roomRepository,
        RoomTypeRepositoryInterface $roomTypeRepository,
        BookingRepositoryInterface $bookingRepository = null
    ) {
        $this->roomRepository = $roomRepository;
        $this->roomTypeRepository = $roomTypeRepository;
        $this->bookingRepository = $bookingRepository;
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
 $todayCheckIns = $this->getTodayCheckIns();
        return new DashboardStatsDTO(
        $totalRooms,
        $availableRooms,
        $occupiedRooms,
        $cleaningRooms,
        $maintenanceRooms,
        $totalRoomTypes,
        $occupancyRate,
        [],
        $this->getRecentBookings(),
        $todayCheckIns,                   
        $this->getTodayCheckOuts(),
        count($todayCheckIns),            
        $this->getPendingBookingsCount()
    );
    }
    
    private function getTodayCheckIns(): array
    {
    return $this->bookingRepository?->findTodayCheckIns(10) ?? [];
    }

    private function getTodayCheckOuts(): array
    {
    return $this->bookingRepository?->findTodayCheckOuts(10) ?? [];
    }

private function getPendingBookingsCount(): int
{
    return $this->bookingRepository?->countPendingBookings() ?? 0;
}

private function getRecentBookings(): array
{
    return $this->bookingRepository?->findRecentWithRoom(10) ?? [];
}


}