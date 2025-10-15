<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\User;

/**
 * User Repository Interface
 */
interface UserRepositoryInterface
{
    public function findById(int $id): ?User;
    public function findByEmail(string $email): ?User;
    public function findAll(): array;
    public function save(User $user): bool;
    public function update(User $user): bool;
    public function delete(int $id): bool;
    public function emailExists(string $email): bool;
}

