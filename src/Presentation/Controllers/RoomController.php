<?php

namespace App\Presentation\Controllers;

use App\Infrastructure\Repositories\RoomRepository;
use App\Core\Database\Database;

/**
 * Room Controller
 */
class RoomController extends BaseController
{
    private RoomRepository $roomRepository;

    public function __construct()
    {
        parent::__construct();
        $dbConfig = require __DIR__ . '/../../../config/database.php';
        $database = Database::getInstance($dbConfig);
        $this->roomRepository = new RoomRepository($database);
    }

    public function index(): void
    {
        $rooms = $this->roomRepository->findAvailable();

        $this->render('pages.rooms.index', [
            'title' => 'Available Rooms',
            'rooms' => $rooms
        ]);
    }

    public function show(int $id): void
    {
        $room = $this->roomRepository->findById($id);

        if (!$room) {
            $this->redirect('/rooms');
            return;
        }

        $this->render('pages.rooms.show', [
            'title' => 'Room Details',
            'room' => $room
        ]);
    }

    public function search(): void
    {
        $checkIn = $_GET['check_in'] ?? null;
        $checkOut = $_GET['check_out'] ?? null;
        $type = $_GET['type'] ?? null;

        $rooms = [];

        if ($checkIn && $checkOut) {
            $checkInDate = new \DateTime($checkIn);
            $checkOutDate = new \DateTime($checkOut);
            $rooms = $this->roomRepository->findAvailableByDateRange($checkInDate, $checkOutDate);
        } else {
            $rooms = $this->roomRepository->findAvailable();
        }

        if ($type) {
            $rooms = array_filter($rooms, fn($room) => $room->getType() === $type);
        }

        $this->render('pages.rooms.index', [
            'title' => 'Search Results',
            'rooms' => $rooms,
            'filters' => compact('checkIn', 'checkOut', 'type')
        ]);
    }
}

