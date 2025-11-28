<?php

namespace App\Application\Interfaces;

use App\Application\DTOs\AdminDTO;

/**
 * Authentication Service Interface
 * 
 * Defines contract for authentication operations
 */
interface AuthServiceInterface
{
    /**
     * Login admin with username and password
     * 
     * @param string $username
     * @param string $password
     * @return AdminDTO
     * @throws Exception
     */
    public function login(string $username, string $password): AdminDTO;

    /**
     * Logout current admin
     */
    public function logout(): void;

    /**
     * Check if admin is logged in
     * 
     * @return bool
     */
    public function isLogged(): bool;

    /**
     * Get current logged in admin
     * 
     * @return AdminDTO|null
     */
    public function getCurrentAdmin(): ?AdminDTO;

    /**
     * Refresh admin session (update last activity)
     */
    public function refreshSession(): void;

    /**
     * Verify password
     * 
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public function verifyPassword(string $password, string $hash): bool;
}
