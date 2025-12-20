<?php
/**
 * Configuraci¨®n de Base de Datos - IFTS 14
 */

class Database
{
    private $host = "localhost";
    private $db_name = "ifts14c8_dev";
    private $username = "ifts14c8";
    private $password = "pb9V5tbhvE9kBPW";

    private $conn = null;

    public function getConnection()
    {
        if ($this->conn !== null) {
            return $this->conn;
        }

        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return $this->conn;
        } catch (PDOException $e) {
            http_response_code(500);
            header("Content-Type: application/json; charset=utf-8");
            echo json_encode([
                "success" => false,
                "error"   => "Error de conexion a la base de datos"
            ]);
            exit();
        }
    }
}
