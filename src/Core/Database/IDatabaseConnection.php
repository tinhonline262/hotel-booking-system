<?php
namespace App\Core\Database;

use  \PDO;

/**
 * Interface này định nghĩa một "hợp đồng" cho các lớp kết nối CSDL.
 * Nó trừu tượng hóa việc lấy đối tượng kết nối (ví dụ: PDO).
 */
interface IDatabaseConnection
{
    /**
     * Lấy đối tượng kết nối PDO.
     *
     * @return PDO
     */
    public function getConnection(): PDO;
}