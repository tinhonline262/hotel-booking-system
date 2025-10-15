<?php

namespace App\Presentation\Controllers;

use App\Infrastructure\Repositories\BookingRepository;
use App\Infrastructure\Repositories\UserRepository;
use App\Core\Database\Database;

/**
 * Dashboard Controller
 */
class DashboardController extends BaseController
{
    private BookingRepository $bookingRepository;
    private UserRepository $userRepository;

    public function __construct()
    {
        parent::__construct();
        $dbConfig = require __DIR__ . '/../../../config/database.php';
        $database = Database::getInstance($dbConfig);
        $this->bookingRepository = new BookingRepository($database);
        $this->userRepository = new UserRepository($database);
    }

    public function index(): void
    {
        $this->requireAuth();

        $bookings = $this->bookingRepository->findByUserId($_SESSION['user_id']);

        $this->render('pages.dashboard.index', [
            'title' => 'Dashboard',
            'bookings' => $bookings
        ]);
    }

    public function bookings(): void
    {
        $this->requireAuth();

        $bookings = $this->bookingRepository->findByUserId($_SESSION['user_id']);

        $this->render('pages.dashboard.bookings', [
            'title' => 'My Bookings',
            'bookings' => $bookings
        ]);
    }

    public function profile(): void
    {
        $this->requireAuth();

        $user = $this->userRepository->findById($_SESSION['user_id']);

        $this->render('pages.dashboard.profile', [
            'title' => 'My Profile',
            'user' => $user
        ]);
    }

    public function updateProfile(): void
    {
        $this->requireAuth();

        $errors = $this->validate($_POST, [
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => 'required'
        ]);

        if (!empty($errors)) {
            $this->render('pages.dashboard.profile', [
                'title' => 'My Profile',
                'errors' => $errors
            ]);
            return;
        }

        $user = $this->userRepository->findById($_SESSION['user_id']);
        $user->updateProfile(
            $_POST['first_name'],
            $_POST['last_name'],
            $_POST['phone']
        );

        $this->userRepository->update($user);

        // Update session
        $_SESSION['user']['name'] = $user->getFullName();

        $_SESSION['success'] = 'Profile updated successfully';
        $this->redirect('/dashboard/profile');
    }
}

