<?php

namespace App\Presentation\Middlewares;

/**
 * Authentication Middleware
 */
class AuthMiddleware
{
    public function handle(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        return true;
    }
}

