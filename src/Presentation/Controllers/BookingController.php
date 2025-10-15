<?php

namespace App\Presentation\Controllers;

use App\Application\UseCases\CreateBookingUseCase;
use App\Infrastructure\Repositories\BookingRepository;
use App\Infrastructure\Repositories\RoomRepository;
use App\Core\Database\Database;
use DateTime;

/**
 * Booking Controller
 */
class BookingController extends BaseController
{
    private BookingRepository $bookingRepository;
    private RoomRepository $roomRepository;
    private CreateBookingUseCase $createBookingUseCase;

    public function __construct()
    {
        parent::__construct();
        $dbConfig = require __DIR__ . '/../../../config/database.php';
        $database = Database::getInstance($dbConfig);
        $this->bookingRepository = new BookingRepository($database);
        $this->roomRepository = new RoomRepository($database);
        $this->createBookingUseCase = new CreateBookingUseCase(
            $this->bookingRepository,
            $this->roomRepository
        );
    }

    public function create(): void
    {
        $this->requireAuth();

        $roomId = $_GET['room_id'] ?? null;
        if (!$roomId) {
            $this->redirect('/rooms');
            return;
        }

        $room = $this->roomRepository->findById($roomId);
        if (!$room) {
            $this->redirect('/rooms');
            return;
        }

        $this->render('pages.booking.create', [
            'title' => 'Book Room',
            'room' => $room,
            'checkIn' => $_GET['check_in'] ?? '',
            'checkOut' => $_GET['check_out'] ?? ''
        ]);
    }

    public function store(): void
    {
        $this->requireAuth();

        $errors = $this->validate($_POST, [
            'room_id' => 'required',
            'check_in_date' => 'required',
            'check_out_date' => 'required',
            'number_of_guests' => 'required'
        ]);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $this->redirect('/booking/create?room_id=' . $_POST['room_id']);
            return;
        }

        try {
            $booking = $this->createBookingUseCase->execute([
                'user_id' => $_SESSION['user_id'],
                'room_id' => $_POST['room_id'],
                'check_in_date' => $_POST['check_in_date'],
                'check_out_date' => $_POST['check_out_date'],
                'number_of_guests' => $_POST['number_of_guests'],
                'special_requests' => $_POST['special_requests'] ?? null
            ]);

            $_SESSION['success'] = 'Booking created successfully!';
            $this->redirect('/booking/confirmation/' . $booking->getId());
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('/booking/create?room_id=' . $_POST['room_id']);
        }
    }

    public function confirmation(int $id): void
    {
        $this->requireAuth();

        $booking = $this->bookingRepository->findById($id);
        if (!$booking || $booking->getUserId() !== $_SESSION['user_id']) {
            $this->redirect('/dashboard');
            return;
        }

        $room = $this->roomRepository->findById($booking->getRoomId());

        $this->render('pages.booking.confirmation', [
            'title' => 'Booking Confirmation',
            'booking' => $booking,
            'room' => $room
        ]);
    }

    public function cancel(int $id): void
    {
        $this->requireAuth();

        $booking = $this->bookingRepository->findById($id);
        if (!$booking || $booking->getUserId() !== $_SESSION['user_id']) {
            $this->redirect('/dashboard');
            return;
        }

        try {
            $booking->cancel();
            $this->bookingRepository->update($booking);
            $_SESSION['success'] = 'Booking cancelled successfully';
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        $this->redirect('/dashboard/bookings');
    }
}

