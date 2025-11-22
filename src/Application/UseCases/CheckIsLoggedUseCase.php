<?php

namespace App\Application\UseCases;

use App\Application\Interfaces\AuthServiceInterface;

/**
 * Check Is Logged Use Case
 * 
 * Checks if admin is currently logged in
 */
class CheckIsLoggedUseCase
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
     * Execute - check if logged in
     * 
     * @return bool
     */
    public function execute(): bool
    {
        return $this->authService->isLogged();
    }
}
