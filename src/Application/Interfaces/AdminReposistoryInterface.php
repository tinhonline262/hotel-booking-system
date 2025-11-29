<?php

namespace App\Domain\Interfaces\Repositories;

use App\Domain\Entities\Admin;

/**
 * Admin Repository Interface
 */
interface AdminRepositoryInterface
{
    /**
     * Find admin by ID
     */
    public function findById(int $id): ?Admin;

    /**
     * Find admin by email
     */
    public function findByEmail(string $email): ?Admin;

    /**
     * Find admin by username
     */
    public function findByUsername(string $username): ?Admin;

    /**
     * Get all admins
     */
    public function findAll(): array;

    /**
     * Create new admin
     */
    public function create(Admin $admin): bool;

    /**
     * Update admin
     */
    public function update(Admin $admin): bool;

    /**
     * Delete admin
     */
    public function delete(int $id): bool;
}