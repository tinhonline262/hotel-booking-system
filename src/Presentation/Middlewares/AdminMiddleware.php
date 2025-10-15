<?php

namespace App\Presentation\Middlewares;

/**
 * Admin Middleware
 */
class AdminMiddleware
{
    public function handle(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: /');
            exit;
        }

        return true;
    }
}

