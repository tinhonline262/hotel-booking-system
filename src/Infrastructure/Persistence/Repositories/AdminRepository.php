<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Entities\Admin;
use App\Domain\Interfaces\Repositories\AdminRepositoryInterface;
use App\Core\Database\Database;

use PDO;

/**
 * Admin Repository Implementation
 */
class AdminRepository implements AdminRepositoryInterface
{
    private PDO $connection;

    public function __construct(Database $database)
    {
        $this->connection = $database->getConnection();
    }

    public function findByEmail(string $email): ?Admin
    {
        $sql = "SELECT * FROM admins WHERE email = :email LIMIT 1";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['email' => $email]);
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row ? $this->mapToEntity($row) : null;
    }

    public function findByUsername(string $username): ?Admin
    {
        $sql = "SELECT * FROM admins WHERE username = :username LIMIT 1";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['username' => $username]);
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row ? $this->mapToEntity($row) : null;
    }

    public function findById(int $id): ?Admin
{
    $sql = "SELECT * FROM admins WHERE id = :id LIMIT 1";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute(['id' => $id]);
    
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $row ? $this->mapToEntity($row) : null;
}

private function mapToEntity(array $row): Admin
{
    return new Admin(
        (int) $row['id'], 
        $row['username'],
        $row['email'],
        $row['password'],  
        $row['full_name'],
        $row['created_at'],
        $row['updated_at']
    );
}
}
