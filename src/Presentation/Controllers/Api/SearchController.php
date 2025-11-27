<?php

namespace App\Presentation\Controllers\Api;

use App\Application\Interfaces\RoomServiceInterface;
use App\Domain\Interfaces\Repositories\BookingRepositoryInterface;

class SearchController extends BaseRestController
{
    private RoomServiceInterface $roomService;
    private BookingRepositoryInterface $bookingRepository;

    public function __construct(
        RoomServiceInterface $roomService,
        BookingRepositoryInterface $bookingRepository
    ) {
        parent::__construct();
        $this->roomService = $roomService;
        $this->bookingRepository = $bookingRepository;
    }

    public function searchRooms(): void
    {
        try {
            // Get all rooms with details
            $rooms = $this->roomService->getAllRoomsWithDetails();

            // Filter out booked/occupied rooms by default
            $rooms = array_filter($rooms, function($room) {
                return isset($room['status']) &&
                       $room['status'] !== 'occupied';
            });

            // Apply filters from query parameters
            $rooms = $this->applyFilters($rooms, $_GET);

            // Re-index array
            $rooms = array_values($rooms);

            $message = count($rooms) > 0
                ? "Found " . count($rooms) . " available room(s)"
                : "No available rooms found matching the criteria";

            $this->success($rooms, $message);

        } catch (\Exception $e) {
            $this->serverError('Failed to search rooms: ' . $e->getMessage());
        }
    }

    public function getAvailableRooms(): void
    {
        try {
            // Get all rooms with details
            $rooms = $this->roomService->getAllRoomsWithDetails();

            // Filter out booked/occupied rooms
            $rooms = array_filter($rooms, function($room) {
                return isset($room['status']) &&
                       $room['status'] !== 'occupied';
            });

            // Re-index array
            $rooms = array_values($rooms);

            $this->success(
                $rooms,
                "Available rooms retrieved successfully (" . count($rooms) . " rooms)"
            );

        } catch (\Exception $e) {
            $this->serverError('Failed to retrieve available rooms: ' . $e->getMessage());
        }
    }

    /**
     * GET /api/search/rooms/dates
     * Search rooms with date range and guest filters
     *
     * Query params:
     * - check_in: Check-in date (Y-m-d format)
     * - check_out: Check-out date (Y-m-d format)
     * - guests: Number of guests
     * - room_type: Filter by room type name
     * - min_price: Minimum price per night
     * - max_price: Maximum price per night
     * - amenities: Comma-separated amenities
     */
    public function searchWithDates(): void
    {
        try {
            $checkIn = $_GET['check_in'] ?? null;
            $checkOut = $_GET['check_out'] ?? null;

            // Get all rooms with details
            $rooms = $this->roomService->getAllRoomsWithDetails();

            // Filter by date availability if dates provided
            if ($checkIn && $checkOut) {
                $rooms = $this->filterByDateAvailability($rooms, $checkIn, $checkOut);
            } else {
                // If no dates, just exclude occupied/booked rooms
                $rooms = array_filter($rooms, function($room) {
                    return isset($room['status']) &&
                           $room['status'] !== 'occupied';
                });
            }

            // Apply additional filters
            $rooms = $this->applyFilters($rooms, $_GET);

            // Calculate total price based on number of nights
            if ($checkIn && $checkOut) {
                $rooms = $this->calculateTotalPrices($rooms, $checkIn, $checkOut);
            }

            // Re-index array
            $rooms = array_values($rooms);

            $message = count($rooms) > 0
                ? "Found " . count($rooms) . " available room(s)"
                : "No available rooms found for the selected dates";

            $this->success($rooms, $message);

        } catch (\Exception $e) {
            $this->serverError('Failed to search rooms: ' . $e->getMessage());
        }
    }

    /**
     * Filter rooms by date availability
     */
    private function filterByDateAvailability(array $rooms, string $checkIn, string $checkOut): array
    {
        $availableRooms = [];

        foreach ($rooms as $room) {
            $roomId = $room['roomId'];

            // First check if room status is available, cleaning, or maintenance (not occupied/booked)
            if (isset($room['status']) && in_array($room['status'], ['occupied'])) {
                continue; // Skip rooms that are already occupied or booked
            }

            // Check if room is available for the requested dates
            $isAvailable = $this->bookingRepository->checkRoomAvailable($roomId, $checkIn, $checkOut);

            if ($isAvailable) {
                $availableRooms[] = $room;
            }
        }

        return $availableRooms;
    }

    /**
     * Calculate total prices based on number of nights
     * Note: Same day check-in and check-out counts as 1 night
     */
    private function calculateTotalPrices(array $rooms, string $checkIn, string $checkOut): array
    {
        $checkInDate = new \DateTime($checkIn);
        $checkOutDate = new \DateTime($checkOut);
        $numberOfNights = $checkInDate->diff($checkOutDate)->days;

        // If same day or 0 days, count as 1 night
        if ($numberOfNights < 1) {
            $numberOfNights = 1;
        }

        foreach ($rooms as &$room) {
            $pricePerNight = (float)$room['pricePerNight'];
            $room['numberOfNights'] = $numberOfNights;
            $room['totalPrice'] = $pricePerNight * $numberOfNights;
        }

        return $rooms;
    }

    /**
     * Apply filters to rooms
     */
    private function applyFilters(array $rooms, array $filters): array
    {
        $filtered = $rooms;

        // Filter by guest capacity (support both 'capacity' and 'guests' keys)
        $guestCount = null;
        if (isset($filters['capacity']) && is_numeric($filters['capacity'])) {
            $guestCount = (int)$filters['capacity'];
        } elseif (isset($filters['guests']) && is_numeric($filters['guests'])) {
            $guestCount = (int)$filters['guests'];
        }

        if ($guestCount !== null) {
            $filtered = array_filter($filtered, function($room) use ($guestCount) {
                return (int)$room['capacity'] >= $guestCount;
            });
        }

        return $filtered;
    }
}
