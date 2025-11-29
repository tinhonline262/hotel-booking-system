<?php

namespace App\Application\Services;

use App\Application\DTOs\LoginDTO;
use App\Application\UseCases\LoginUseCase;
use App\Application\UseCases\LogoutUseCase;
use App\Domain\Entities\Admin;

/**
 * Auth Service - Refactored để tránh code lặp
 */
class AuthService
{
    private LoginUseCase $loginUseCase;
    private LogoutUseCase $logoutUseCase;

    public function __construct(
        LoginUseCase $loginUseCase,
        LogoutUseCase $logoutUseCase
    ) {
        $this->loginUseCase = $loginUseCase;
        $this->logoutUseCase = $logoutUseCase;
    }

    /**
     * Login admin and create session
     */
    public function login(array $data): array
    {
        $dto = LoginDTO::fromArray($data);
        $result = $this->loginUseCase->execute($dto);

        if ($result['success'] && $result['admin'] instanceof Admin) {
            $this->createSession($result['admin']);
        }

        return $result;
    }

    /**
     * Logout admin and destroy session
     */
    public function logout(): array
    {
        return $this->logoutUseCase->execute();
    }

    /**
     * Check if admin is authenticated
     */
    public function isAuthenticated(): bool
    {
        $this->ensureSession();
        return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
    }

    /**
     * Get current authenticated admin data
     */
    public function getCurrentAdmin(): ?array
    {
        if (!$this->isAuthenticated()) {
            return null;
        }

        return [
            'admin_id' => $_SESSION['admin_id'] ?? null,
            'username' => $_SESSION['username'] ?? null,
            'email' => $_SESSION['email'] ?? null,
            'full_name' => $_SESSION['full_name'] ?? null,
        ];
    }

    /**
     * REFACTOR: Đảm bảo session đã khởi động
     * Thay thế code lặp lại 3 lần
     */
    private function ensureSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Create admin session
     */
    private function createSession(Admin $admin): void
    {
        $this->ensureSession();

        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);

        // Store admin data in session
        $_SESSION['admin_id'] = $admin->getId();
        $_SESSION['username'] = $admin->getUsername();
        $_SESSION['email'] = $admin->getEmail();
        $_SESSION['full_name'] = $admin->getFullName();
        $_SESSION['logged_in_at'] = time();
    }
}