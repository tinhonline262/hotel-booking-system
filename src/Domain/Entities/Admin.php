<?php

namespace App\Domain\Entities;

/**
 * Admin Entity
 */
class Admin
{
    private int $id;
    private string $username;
    private string $email;
    private string $passwordHash;
    private string $fullName;
    private string $createdAt;
    private string $updatedAt;

    public function __construct(
        int $id,
        string $username,
        string $email,
        string $passwordHash,
        string $fullName,
        string $createdAt,
        string $updatedAt
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->fullName = $fullName;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }

    /**
     * Verify password
     */
    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->passwordHash);
    }

    /**
     * Convert entity to array (for API responses)
     * IMPORTANT: Không trả về password hash!
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'full_name' => $this->fullName,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];
    }

    /**
     * Set new password
     */
    public function setPassword(string $newPassword): void
    {
        $this->passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
    }

    /**
     * Update profile
     */
    public function updateProfile(string $fullName, string $email): void
    {
        $this->fullName = $fullName;
        $this->email = $email;
        $this->updatedAt = date('Y-m-d H:i:s');
    }
}