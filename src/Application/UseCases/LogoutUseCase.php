<?php

namespace App\Application\UseCases;

/**
 * Logout Use Case
 * Application Layer - Business Logic
 */
class LogoutUseCase
{
    /**
     * Execute logout
     * Clears session data
     * 
     * @return array ['success' => bool, 'message' => string]
     */
    public function execute(): array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Unset all session variables
        $_SESSION = [];

        // Destroy session cookie
        if (isset($_COOKIE[session_name()])) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        // Destroy session
        session_destroy();

        return [
            'success' => true,
            'message' => 'Logout successful'
        ];
    }
}