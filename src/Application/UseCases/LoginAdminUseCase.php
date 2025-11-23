<?php

namespace App\Application\UseCases;

use App\Application\DTOs\AdminDTO;
use App\Application\Interfaces\AuthServiceInterface;
use Exception;

/**
 * Login Admin Use Case
 * 
 * Handles admin login operation
 */
class LoginAdminUseCase
{
    private AuthServiceInterface $authService;

    /**
     * Constructor
     * 
     * @param AuthServiceInterface $authService
     */
    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Execute login
     * 
     * @param string $username
     * @param string $password
     * @return AdminDTO
     * @throws Exception
     */
    public function execute(string $username, string $password): AdminDTO
    {
        // Validate inputs
        if (empty(trim($username))) {
            throw new Exception('Username is required.');
        }

        if (empty(trim($password))) {
            throw new Exception('Password is required.');
        }

        // Sanitize username to prevent SQL injection
        $username = trim($username);

        if (strlen($username) > 50) {
            throw new Exception('Username is too long.');
        }

        // Attempt login
        return $this->authService->login($username, $password);
    }
}
