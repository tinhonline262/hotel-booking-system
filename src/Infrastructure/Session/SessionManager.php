<?php

namespace App\Infrastructure\Session;

use Exception;

/**
 * Session Manager
 * 
 * Handles session operations, timeouts, and CSRF protection
 */
class SessionManager
{
    private const SESSION_TIMEOUT = 7200; // 2 hours in seconds
    private const CSRF_TOKEN_LENGTH = 32;

    /**
     * Initialize session with security settings
     */
    public static function init(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            // Set secure session configuration
            ini_set('session.use_strict_mode', '1');
            ini_set('session.use_only_cookies', '1');
            ini_set('session.cookie_httponly', '1');
            
            session_start();
        }

        // Check and handle session timeout
        self::checkTimeout();
    }

    /**
     * Check session timeout
     */
    private static function checkTimeout(): void
    {
        if (isset($_SESSION['admin_id']) && isset($_SESSION['last_activity'])) {
            $elapsed = time() - $_SESSION['last_activity'];

            if ($elapsed > self::SESSION_TIMEOUT) {
                // Session expired
                self::destroy();
                return;
            }
        }

        // Update last activity time
        $_SESSION['last_activity'] = time();
    }

    /**
     * Regenerate session ID for security
     */
    public static function regenerate(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    /**
     * Set admin session
     * 
     * @param int $adminId
     * @param array $adminData
     */
    public static function setAdminSession(int $adminId, array $adminData): void
    {
        self::init();
        self::regenerate();

        $_SESSION['admin_id'] = $adminId;
        $_SESSION['admin'] = $adminData;
        $_SESSION['last_activity'] = time();
        $_SESSION['csrf_token'] = self::generateCSRFToken();
    }

    /**
     * Get admin session data
     * 
     * @return array|null
     */
    public static function getAdminSession(): ?array
    {
        self::init();

        if (!isset($_SESSION['admin_id'])) {
            return null;
        }

        return $_SESSION['admin'] ?? null;
    }

    /**
     * Get current admin ID
     * 
     * @return int|null
     */
    public static function getAdminId(): ?int
    {
        self::init();

        return $_SESSION['admin_id'] ?? null;
    }

    /**
     * Check if admin is logged in
     * 
     * @return bool
     */
    public static function isLogged(): bool
    {
        self::init();

        return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
    }

    /**
     * Destroy session
     */
    public static function destroy(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
            session_destroy();
        }
    }

    /**
     * Generate CSRF token
     * 
     * @return string
     * @throws Exception
     */
    public static function generateCSRFToken(): string
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(self::CSRF_TOKEN_LENGTH));
        }

        return $_SESSION['csrf_token'];
    }

    /**
     * Get CSRF token
     * 
     * @return string|null
     */
    public static function getCSRFToken(): ?string
    {
        self::init();

        return $_SESSION['csrf_token'] ?? null;
    }

    /**
     * Verify CSRF token
     * 
     * @param string $token
     * @return bool
     */
    public static function verifyCSRFToken(string $token): bool
    {
        self::init();

        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }

        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Get session timeout value
     * 
     * @return int
     */
    public static function getTimeout(): int
    {
        return self::SESSION_TIMEOUT;
    }

    /**
     * Get remaining session time
     * 
     * @return int
     */
    public static function getRemainingTime(): int
    {
        self::init();

        if (!isset($_SESSION['last_activity'])) {
            return 0;
        }

        $elapsed = time() - $_SESSION['last_activity'];
        $remaining = max(0, self::SESSION_TIMEOUT - $elapsed);

        return $remaining;
    }
}
