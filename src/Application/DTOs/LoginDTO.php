<?php

namespace App\Application\DTOs;

/**
 * Login Data Transfer Object
 * Application Layer
 */
class LoginDTO
{
    public string $identifier; // Can be email or username
    public string $password;
    public bool $rememberMe;

    public function __construct(string $identifier, string $password, bool $rememberMe = false)
    {
        $this->identifier = trim($identifier);
        $this->password = $password;
        $this->rememberMe = $rememberMe;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['identifier'] ?? $data['email'] ?? $data['username'] ?? '',
            $data['password'] ?? '',
            (bool) ($data['remember_me'] ?? false)
        );
    }

    public function isEmail(): bool
    {
        return filter_var($this->identifier, FILTER_VALIDATE_EMAIL) !== false;
    }
}