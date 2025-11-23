<?php

namespace App\Application\DTOs;

/**
 * Admin Data Transfer Object
 * 
 * Used for transferring admin data between layers
 */
class AdminDTO
{
    public int $id;
    public string $username;
    public string $fullName;
    public string $email;
    public ?string $password; // Set only when needed, typically null for security
    public string $createdAt;
    public string $updatedAt;

    /**
     * Constructor
     * 
     * @param int $id
     * @param string $username
     * @param string $fullName
     * @param string $email
     * @param string $createdAt
     * @param string $updatedAt
     * @param string|null $password
     */
    public function __construct(
        int $id,
        string $username,
        string $fullName,
        string $email,
        string $createdAt,
        string $updatedAt,
        ?string $password = null
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->fullName = $fullName;
        $this->email = $email;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->password = $password;
    }

    /**
     * Convert to array (exclude password for security)
     * 
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'fullName' => $this->fullName,
            'email' => $this->email,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }

    /**
     * Convert to array for session storage
     * 
     * @return array
     */
    public function toSessionArray(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'fullName' => $this->fullName,
            'email' => $this->email,
        ];
    }
}
