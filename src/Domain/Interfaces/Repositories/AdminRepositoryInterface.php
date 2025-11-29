<?php

namespace App\Domain\Interfaces\Repositories;

use App\Domain\Entities\Admin;

interface AdminRepositoryInterface
{
    public function findById(int $id): ?Admin;
    public function findByEmail(string $email): ?Admin;
    public function findByUsername(string $username): ?Admin;
    
    // XÓA 4 methods này nếu chưa dùng:
    // public function findAll(): array;
    // public function create(Admin $admin): bool;
    // public function update(Admin $admin): bool;
    // public function delete(int $id): bool;
}