<?php

namespace App\Application\UseCases;

use App\Application\DTOs\LoginDTO;
use App\Application\Validators\LoginValidator;
use App\Domain\Entities\Admin;
use App\Domain\Interfaces\Repositories\AdminRepositoryInterface;

/**
 * Login Use Case - Simplified
 */
class LoginUseCase
{
    private AdminRepositoryInterface $adminRepository;
    private LoginValidator $validator;

    public function __construct(
        AdminRepositoryInterface $adminRepository,
        LoginValidator $validator
    ) {
        $this->adminRepository = $adminRepository;
        $this->validator = $validator;
    }

    /**
     * Execute login
     * 
     * @return array ['success' => bool, 'admin' => Admin|null, 'errors' => array, 'message' => string]
     */
    public function execute(LoginDTO $dto): array
    {
        // Validate input
        $errors = $this->validator->validate($dto);
        if (!empty($errors)) {
            return [
                'success' => false,
                'admin' => null,
                'errors' => $errors,
                'message' => 'Validation failed'
            ];
        }

        // Find admin by email or username
        $admin = $dto->isEmail()
            ? $this->adminRepository->findByEmail($dto->identifier)
            : $this->adminRepository->findByUsername($dto->identifier);

        // Check if admin exists
        if ($admin === null) {
            return [
                'success' => false,
                'admin' => null,
                'errors' => ['identifier' => 'Invalid credentials'],
                'message' => 'Admin not found'
            ];
        }

        // Verify password
        if (!$admin->verifyPassword($dto->password)) {
            return [
                'success' => false,
                'admin' => null,
                'errors' => ['password' => 'Invalid credentials'],
                'message' => 'Invalid password'
            ];
        }

        return [
            'success' => true,
            'admin' => $admin,
            'errors' => [],
            'message' => 'Login successful'
        ];
    }
}