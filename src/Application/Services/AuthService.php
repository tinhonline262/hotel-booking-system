<?php

namespace App\Application\Services;

use App\Application\DTOs\AdminDTO;
use App\Application\Interfaces\AdminRepositoryInterface;
use App\Application\Interfaces\AuthServiceInterface;
use App\Infrastructure\Session\SessionManager;
use Exception;

/**
 * Authentication Service
 * 
 * Handles admin authentication, session management, and password verification
 */
class AuthService implements AuthServiceInterface
{
    private AdminRepositoryInterface $adminRepository;

    /**
     * Constructor
     * 
     * @param AdminRepositoryInterface $adminRepository
     */
    public function __construct(AdminRepositoryInterface $adminRepository)
    {
        $this->adminRepository = $adminRepository;
    }

    /**
     * Login admin with username and password
     * 
     * @param string $username
     * @param string $password
     * @return AdminDTO
     * @throws Exception
     */
    public function login(string $username, string $password): AdminDTO
    {
        try {
            // Find admin by username
            $admin = $this->adminRepository->findByUsername($username);

            if (!$admin) {
                throw new Exception('Username or password is incorrect.');
            }

            // Verify password
            if (!$this->verifyPassword($password, $admin->password)) {
                throw new Exception('Username or password is incorrect.');
            }

            // Set admin session
            SessionManager::setAdminSession($admin->id, $admin->toSessionArray());

            // Return admin without password
            $admin->password = null;

            return $admin;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Logout current admin
     */
    public function logout(): void
    {
        SessionManager::destroy();
    }

    /**
     * Check if admin is logged in
     * 
     * @return bool
     */
    public function isLogged(): bool
    {
        return SessionManager::isLogged();
    }

    /**
     * Get current logged in admin
     * 
     * @return AdminDTO|null
     */
    public function getCurrentAdmin(): ?AdminDTO
    {
        $adminData = SessionManager::getAdminSession();

        if (!$adminData) {
            return null;
        }

        try {
            $admin = $this->adminRepository->findById($adminData['id']);
            if ($admin) {
                $admin->password = null; // Never return password
            }
            return $admin;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Refresh admin session (update last activity)
     */
    public function refreshSession(): void
    {
        if ($this->isLogged()) {
            SessionManager::init(); // This updates last_activity
        }
    }

    /**
     * Verify password
     * 
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
