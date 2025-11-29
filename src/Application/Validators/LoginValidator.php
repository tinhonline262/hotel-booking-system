<?php

namespace App\Application\Validators;

use App\Application\DTOs\LoginDTO;

/**
 * Login Validator - Simplified for Admin System
 */
class LoginValidator
{
    /**
     * Validate login data
     */
    public function validate(LoginDTO $dto): array
    {
        $errors = [];

        // Identifier validation (username or email)
        if (empty($dto->identifier)) {
            $errors['identifier'] = 'Username or email is required';
        }

        // Password validation
        if (empty($dto->password)) {
            $errors['password'] = 'Password is required';
        }

        return $errors;
    }
}