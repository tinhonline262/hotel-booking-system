<?php

namespace App\Presentation\Controllers;

use App\Application\UseCases\LoginAdminUseCase;
use App\Application\UseCases\LogoutAdminUseCase;
use App\Application\UseCases\GetCurrentAdminUseCase;
use App\Application\UseCases\CheckIsLoggedUseCase;
use App\Infrastructure\Session\SessionManager;
use Exception;

/**
 * Auth Controller
 * 
 * Handles authentication operations (login, logout, check login status)
 */
class AuthController
{
    private LoginAdminUseCase $loginAdminUseCase;
    private LogoutAdminUseCase $logoutAdminUseCase;
    private GetCurrentAdminUseCase $getCurrentAdminUseCase;
    private CheckIsLoggedUseCase $checkIsLoggedUseCase;

    /**
     * Constructor
     * 
     * @param LoginAdminUseCase $loginAdminUseCase
     * @param LogoutAdminUseCase $logoutAdminUseCase
     * @param GetCurrentAdminUseCase $getCurrentAdminUseCase
     * @param CheckIsLoggedUseCase $checkIsLoggedUseCase
     */
    public function __construct(
        LoginAdminUseCase $loginAdminUseCase,
        LogoutAdminUseCase $logoutAdminUseCase,
        GetCurrentAdminUseCase $getCurrentAdminUseCase,
        CheckIsLoggedUseCase $checkIsLoggedUseCase
    ) {
        $this->loginAdminUseCase = $loginAdminUseCase;
        $this->logoutAdminUseCase = $logoutAdminUseCase;
        $this->getCurrentAdminUseCase = $getCurrentAdminUseCase;
        $this->checkIsLoggedUseCase = $checkIsLoggedUseCase;
    }

    /**
     * Show login page
     */
    public function showLogin(): void
    {
        // Initialize session
        SessionManager::init();

        // If already logged in, redirect to dashboard
        if ($this->checkIsLoggedUseCase->execute()) {
            header('Location: /dashboard');
            exit;
        }

        // Generate CSRF token for form
        $csrfToken = SessionManager::generateCSRFToken();

        // Prepare view variables (used directly by the view)
        $error = $_SESSION['login_error'] ?? null;
        $success = $_SESSION['login_success'] ?? null;

        // Clear flash messages after use
        unset($_SESSION['login_error']);
        unset($_SESSION['login_success']);

        // Render login view
        require_once __DIR__ . '/../Views/login.php';
    }

    /**
     * Handle login form submission
     */
    public function handleLogin(): void
    {
        // Initialize session
        SessionManager::init();

        // Verify request method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /login');
            exit;
        }

        try {
            // Verify CSRF token
            $csrfToken = $_POST['csrf_token'] ?? null;

            if (!$csrfToken || !SessionManager::verifyCSRFToken($csrfToken)) {
                throw new Exception('CSRF token verification failed. Please try again.');
            }

            // Get username and password
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            // Perform login
            $admin = $this->loginAdminUseCase->execute($username, $password);

            // Set success message
            $_SESSION['login_success'] = 'Login successful!';

            // Redirect to dashboard
            header('Location: /dashboard');
            exit;
        } catch (Exception $e) {
            // Set error message in session
            $_SESSION['login_error'] = $e->getMessage();

            // Redirect back to login
            header('Location: /login');
            exit;
        }
    }

    /**
     * Handle logout
     */
    public function handleLogout(): void
    {
        try {
            $this->logoutAdminUseCase->execute();

            // Set success message
            $_SESSION['logout_success'] = 'You have been logged out successfully.';

            // Redirect to login
            header('Location: /login');
            exit;
        } catch (Exception $e) {
            // Even if error occurs, try to destroy session and redirect
            SessionManager::destroy();
            header('Location: /login');
            exit;
        }
    }

    /**
     * API: Check if admin is logged in
     */
    public function checkIsLogged(): void
    {
        header('Content-Type: application/json');

        $isLogged = $this->checkIsLoggedUseCase->execute();

        echo json_encode([
            'success' => true,
            'isLogged' => $isLogged,
            'remainingTime' => SessionManager::getRemainingTime(),
        ]);
    }

    /**
     * API: Get current admin info
     */
    public function getCurrentAdmin(): void
    {
        header('Content-Type: application/json');

        try {
            $admin = $this->getCurrentAdminUseCase->execute();

            if (!$admin) {
                throw new Exception('Admin not logged in.');
            }

            echo json_encode([
                'success' => true,
                'admin' => $admin->toArray(),
                'remainingTime' => SessionManager::getRemainingTime(),
            ]);
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * API: Refresh session (keep alive)
     */
    public function refreshSession(): void
    {
        header('Content-Type: application/json');

        try {
            if (!$this->checkIsLoggedUseCase->execute()) {
                throw new Exception('Not logged in.');
            }

            // Refresh session
            SessionManager::init();

            echo json_encode([
                'success' => true,
                'message' => 'Session refreshed.',
                'remainingTime' => SessionManager::getRemainingTime(),
            ]);
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
