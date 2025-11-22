<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Application\Interfaces\AdminRepositoryInterface;
use App\Application\DTOs\AdminDTO;
use App\Core\Database\Database;
use Exception;

/**
 * Admin Repository Implementation
 * 
 * Handles database operations for Admin entity
 */
class AdminRepository implements AdminRepositoryInterface
{
    private Database $database;

    /**
     * Constructor
     * 
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * Find admin by username
     * 
     * @param string $username
     * @return AdminDTO|null
     * @throws Exception
     */
    public function findByUsername(string $username): ?AdminDTO
    {
        try {
            $query = "SELECT id, username, password, full_name, email, created_at, updated_at 
                     FROM admins 
                     WHERE username = ?";
            
            $result = $this->database->query($query, [$username])->fetch();

            if (!$result) {
                return null;
            }

            return new AdminDTO(
                (int)$result['id'],
                $result['username'],
                $result['full_name'],
                $result['email'],
                $result['created_at'],
                $result['updated_at'],
                $result['password']
            );
        } catch (Exception $e) {
            throw new Exception("Error finding admin by username: " . $e->getMessage());
        }
    }

    /**
     * Find admin by ID
     * 
     * @param int $id
     * @return AdminDTO|null
     * @throws Exception
     */
    public function findById(int $id): ?AdminDTO
    {
        try {
            $query = "SELECT id, username, password, full_name, email, created_at, updated_at 
                     FROM admins 
                     WHERE id = ?";
            
            $result = $this->database->query($query, [$id])->fetch();

            if (!$result) {
                return null;
            }

            return new AdminDTO(
                (int)$result['id'],
                $result['username'],
                $result['full_name'],
                $result['email'],
                $result['created_at'],
                $result['updated_at'],
                $result['password']
            );
        } catch (Exception $e) {
            throw new Exception("Error finding admin by ID: " . $e->getMessage());
        }
    }

    /**
     * Create new admin
     * 
     * @param string $username
     * @param string $password
     * @param string $fullName
     * @param string $email
     * @return AdminDTO
     * @throws Exception
     */
    public function create(string $username, string $password, string $fullName, string $email): AdminDTO
    {
        try {
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            $query = "INSERT INTO admins (username, password, full_name, email) 
                     VALUES (?, ?, ?, ?)";
            
            $this->database->query($query, [$username, $hashedPassword, $fullName, $email]);

            // Get the created admin
            $lastId = $this->database->lastInsertId();
            return $this->findById($lastId);
        } catch (Exception $e) {
            throw new Exception("Error creating admin: " . $e->getMessage());
        }
    }

    /**
     * Update admin
     * 
     * @param int $id
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function update(int $id, array $data): bool
    {
        try {
            $updateFields = [];
            $values = [];

            // Whitelist allowed fields
            $allowedFields = ['username', 'full_name', 'email'];

            foreach ($data as $field => $value) {
                if (in_array($field, $allowedFields)) {
                    $updateFields[] = "$field = ?";
                    $values[] = $value;
                }
            }

            if (empty($updateFields)) {
                return false;
            }

            $values[] = $id;
            $query = "UPDATE admins SET " . implode(", ", $updateFields) . " WHERE id = ?";

            $this->database->query($query, $values);
            return true;
        } catch (Exception $e) {
            throw new Exception("Error updating admin: " . $e->getMessage());
        }
    }

    /**
     * Delete admin
     * 
     * @param int $id
     * @return bool
     * @throws Exception
     */
    public function delete(int $id): bool
    {
        try {
            $query = "DELETE FROM admins WHERE id = ?";
            $this->database->query($query, [$id]);
            return true;
        } catch (Exception $e) {
            throw new Exception("Error deleting admin: " . $e->getMessage());
        }
    }
}
