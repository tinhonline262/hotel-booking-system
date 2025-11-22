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
            $this->getTodayCheckIns(),
            $this->getTodayCheckOuts(),
            count($this->getTodayCheckIns()),
            $this->getPendingBookingsCount()
        );
    }

    private function getTodayCheckIns(): array
    {
        if (!$this->bookingRepository) {
            return [];
        }
        
        try {
            $bookings = $this->bookingRepository->findAll();
            $today = date('Y-m-d');
            $todayCheckIns = [];
            
            foreach ($bookings as $booking) {
                if ($booking->getCheckInDate() && 
                    date('Y-m-d', strtotime($booking->getCheckInDate())) === $today &&
                    in_array($booking->getStatus(), ['confirmed', 'pending'])) {
                    
                    $room = $this->roomRepository->findById($booking->getRoomId());
                    $todayCheckIns[] = [
                        'id' => $booking->getId(),
                        'booking_code' => $booking->getBookingCode(),
                        'customer_name' => $booking->getCustomerName(),
                        'room_number' => $room ? $room->getRoomNumber() : 'N/A',
                        'check_in_date' => $booking->getCheckInDate(),
                        'check_in_time' => $booking->getCheckInDate() ? $this->formatTime($booking->getCheckInDate()) : '09:00',
                        'status' => $booking->getStatus()
                    ];
                }
            }
            
            // Sort by time
            usort($todayCheckIns, function($a, $b) {
                return strtotime($a['check_in_time']) - strtotime($b['check_in_time']);
            });
            
            return array_slice($todayCheckIns, 0, 10);
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getTodayCheckOuts(): array
    {
        if (!$this->bookingRepository) {
            return [];
        }
        
        try {
            $bookings = $this->bookingRepository->findAll();
            $today = date('Y-m-d');
            $todayCheckOuts = [];
            
            foreach ($bookings as $booking) {
                if ($booking->getCheckOutDate() && 
                    date('Y-m-d', strtotime($booking->getCheckOutDate())) === $today &&
                    $booking->getStatus() === 'checked_in') {
                    
                    $room = $this->roomRepository->findById($booking->getRoomId());
                    $todayCheckOuts[] = [
                        'id' => $booking->getId(),
                        'booking_code' => $booking->getBookingCode(),
                        'customer_name' => $booking->getCustomerName(),
                        'room_number' => $room ? $room->getRoomNumber() : 'N/A',
                        'check_out_date' => $booking->getCheckOutDate(),
                        'status' => $booking->getStatus()
                    ];
                }
            }
            
            return array_slice($todayCheckOuts, 0, 10);
        } catch (\Exception $e) {
            return [];
        }
    }

    private function formatTime($dateStr): string
    {
        if (!$dateStr) return '09:00';
        try {
            return date('H:i', strtotime($dateStr));
        } catch (\Exception $e) {
            return '09:00';
        }
    }

    private function getPendingBookingsCount(): int
    {
        if (!$this->bookingRepository) {
            return 0;
        }
        
        try {
            $bookings = $this->bookingRepository->findAll();
            $pendingCount = 0;
            
            foreach ($bookings as $booking) {
                if ($booking->getStatus() === 'pending') {
                    $pendingCount++;
                }
            }
            
            return $pendingCount;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getRecentBookings(): array
    {
        if (!$this->bookingRepository) {
            return [];
        }
        
        try {
            $bookings = $this->bookingRepository->findAll();
            // Get last 10 bookings, sorted by created_at descending
            usort($bookings, function($a, $b) {
                return strtotime($b->getCreatedAt() ?? '0') - strtotime($a->getCreatedAt() ?? '0');
            });
            
            $recentBookings = [];
            foreach (array_slice($bookings, 0, 10) as $booking) {
                $room = $this->roomRepository->findById($booking->getRoomId());
                $recentBookings[] = [
                    'id' => $booking->getId(),
                    'booking_code' => $booking->getBookingCode(),
                    'customer_name' => $booking->getCustomerName(),
                    'room_number' => $room ? $room->getRoomNumber() : 'N/A',
                    'check_in_date' => $booking->getCheckInDate(),
                    'status' => $booking->getStatus(),
                    'created_at' => $booking->getCreatedAt()
                ];
            }
            return $recentBookings;
        } catch (\Exception $e) {
            return [];
        }
    }
}