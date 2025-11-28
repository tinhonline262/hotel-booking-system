<?php

namespace App\Application\UseCases;

use App\Application\DTOs\AdminDTO;
use App\Application\Interfaces\AuthServiceInterface;

/**
 * Get Current Admin Use Case
 * 
 * Retrieves currently logged in admin information
 */
class GetCurrentAdminUseCase
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
     * Execute - get current admin
     * 
     * @return AdminDTO|null
     */
    public function execute(): ?AdminDTO
    {
        if (!$this->authService->isLogged()) {
            return null;
        }

        return $this->authService->getCurrentAdmin();
    }
}
