<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\User;
use App\Domain\Repositories\UserRepositoryInterface;
use App\Core\Database\Database;

/**
 * User Repository Implementation
 */
class UserRepository implements UserRepositoryInterface
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function findById(int $id): ?User
    {
        $stmt = $this->database->query("SELECT * FROM users WHERE id = ?", [$id]);
        $data = $stmt->fetch();
        return $data ? $this->mapToEntity($data) : null;
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->database->query("SELECT * FROM users WHERE email = ?", [$email]);
        $data = $stmt->fetch();
        return $data ? $this->mapToEntity($data) : null;
    }

    public function findAll(): array
    {
        $stmt = $this->database->query("SELECT * FROM users ORDER BY created_at DESC");
        $users = [];

        while ($data = $stmt->fetch()) {
            $users[] = $this->mapToEntity($data);
        }

        return $users;
    }

    public function save(User $user): bool
    {
        $sql = "INSERT INTO users (first_name, last_name, email, password, phone, role, is_active, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

        $this->database->query($sql, [
            $user->getFirstName(),
            $user->getLastName(),
            $user->getEmail(),
            $user->getPassword(),
            $user->getPhone(),
            $user->getRole(),
            $user->isActive() ? 1 : 0
        ]);

        return true;
    }

    public function update(User $user): bool
    {
        $sql = "UPDATE users SET 
                first_name = ?, last_name = ?, email = ?, password = ?,
                phone = ?, role = ?, is_active = ?, updated_at = NOW()
                WHERE id = ?";

        $this->database->query($sql, [
            $user->getFirstName(),
            $user->getLastName(),
            $user->getEmail(),
            $user->getPassword(),
            $user->getPhone(),
            $user->getRole(),
            $user->isActive() ? 1 : 0,
            $user->getId()
        ]);

        return true;
    }

    public function delete(int $id): bool
    {
        $this->database->query("DELETE FROM users WHERE id = ?", [$id]);
        return true;
    }

    public function emailExists(string $email): bool
    {
        $stmt = $this->database->query("SELECT COUNT(*) as count FROM users WHERE email = ?", [$email]);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }

    private function mapToEntity(array $data): User
    {
        return new User(
            (int)$data['id'],
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $data['password'],
            $data['phone'],
            $data['role'],
            (bool)$data['is_active']
        );
    }
}

