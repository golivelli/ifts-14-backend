<?php
/**
 * Configuración de Base de Datos - IFTS 14
 * VERSIÓN CORREGIDA
 */

class Database
{
    // USAR ESTOS VALORES EXACTOS:
    private $host = "localhost";
    private $db_name = "ifts14c8_dev";
    private $username = "ifts14c8";
    private $password = "pb9V5tbhvE9kBPW";
    public $conn;

    public function getConnection()
    {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            return $this->conn;
            
        } catch (PDOException $e) {
            throw new Exception("Error de conexion a BD: " . $e->getMessage());
        }
    }
}   