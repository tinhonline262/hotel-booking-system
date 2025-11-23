<?php

namespace App\Application\Interfaces;

use App\Application\DTOs\AdminDTO;

/**
 * Admin Repository Interface
 * 
 * Defines contract for Admin data access operations
 */
interface AdminRepositoryInterface
{
    /**
     * Find admin by username
     * 
     * @param string $username
     * @return AdminDTO|null
     */
    public function findByUsername(string $username): ?AdminDTO;

    /**
     * Find admin by ID
     * 
     * @param int $id
     * @return AdminDTO|null
     */
    public function findById(int $id): ?AdminDTO;

    /**
     * Create new admin
     * 
     * @param string $username
     * @param string $password (will be hashed)
     * @param string $fullName
     * @param string $email
     * @return AdminDTO
     */
    public function create(string $username, string $password, string $fullName, string $email): AdminDTO;

    /**
     * Update admin
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete admin
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;
}
