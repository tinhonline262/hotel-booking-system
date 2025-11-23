<?php

namespace App\Application\UseCases;

use App\Application\Interfaces\AuthServiceInterface;

/**
 * Logout Admin Use Case
 * 
 * Handles admin logout operation
 */
class LogoutAdminUseCase
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
     * Execute logout
     */
    public function execute(): void
    {
        $this->authService->logout();
    }
}
