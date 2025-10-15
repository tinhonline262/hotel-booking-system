<?php

namespace App\Presentation\Controllers;

use App\Domain\Entities\User;
use App\Infrastructure\Repositories\UserRepository;
use App\Core\Database\Database;

/**
 * Authentication Controller
 */
class AuthController extends BaseController
{
    private UserRepository $userRepository;

    public function __construct()
    {
        parent::__construct();
        $dbConfig = require __DIR__ . '/../../../config/database.php';
        $database = Database::getInstance($dbConfig);
        $this->userRepository = new UserRepository($database);
    }

    public function showLogin(): void
    {
        if ($this->isAuthenticated()) {
            $this->redirect('/dashboard');
            return;
        }

        $this->render('pages.auth.login', [
            'title' => 'Login'
        ]);
    }

    public function login(): void
    {
        $errors = $this->validate($_POST, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!empty($errors)) {
            $this->render('pages.auth.login', [
                'title' => 'Login',
                'errors' => $errors,
                'old' => $_POST
            ]);
            return;
        }

        $user = $this->userRepository->findByEmail($_POST['email']);

        if (!$user || !$user->verifyPassword($_POST['password'])) {
            $this->render('pages.auth.login', [
                'title' => 'Login',
                'errors' => ['email' => 'Invalid credentials'],
                'old' => $_POST
            ]);
            return;
        }

        if (!$user->isActive()) {
            $this->render('pages.auth.login', [
                'title' => 'Login',
                'errors' => ['email' => 'Account is inactive'],
                'old' => $_POST
            ]);
            return;
        }

        // Set session
        $_SESSION['user_id'] = $user->getId();
        $_SESSION['user'] = [
            'id' => $user->getId(),
            'name' => $user->getFullName(),
            'email' => $user->getEmail(),
            'role' => $user->getRole()
        ];

        $this->redirect($user->isAdmin() ? '/admin' : '/dashboard');
    }

    public function showRegister(): void
    {
        if ($this->isAuthenticated()) {
            $this->redirect('/dashboard');
            return;
        }

        $this->render('pages.auth.register', [
            'title' => 'Register'
        ]);
    }

    public function register(): void
    {
        $errors = $this->validate($_POST, [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6',
            'phone' => 'required'
        ]);

        if (!empty($errors)) {
            $this->render('pages.auth.register', [
                'title' => 'Register',
                'errors' => $errors,
                'old' => $_POST
            ]);
            return;
        }

        // Check if email already exists
        if ($this->userRepository->emailExists($_POST['email'])) {
            $this->render('pages.auth.register', [
                'title' => 'Register',
                'errors' => ['email' => 'Email already registered'],
                'old' => $_POST
            ]);
            return;
        }

        // Create user
        $user = new User(
            null,
            $_POST['first_name'],
            $_POST['last_name'],
            $_POST['email'],
            password_hash($_POST['password'], PASSWORD_BCRYPT),
            $_POST['phone'],
            'customer',
            true
        );

        $this->userRepository->save($user);

        $_SESSION['success'] = 'Registration successful! Please login.';
        $this->redirect('/login');
    }

    public function logout(): void
    {
        session_destroy();
        $this->redirect('/');
    }
}

