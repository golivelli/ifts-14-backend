<?php
/**
 * Configuración de Base de Datos - IFTS 14
 * 
 * Este archivo maneja la conexión a MySQL usando PDO
 * Compatible con cPanel hosting
 */

// Headers para CORS (permitir peticiones desde Angular)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Manejar preflight requests de CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

class Database
{
    // Credenciales de base de datos (desde tu .env)
    private $host = "186.22.245.92";
    private $db_name = "ifts14c8_dev";
    private $username = "ifts14c8";
    private $password = "pb9V5tbhvE9kBPW";
    public $conn;

    /**
     * Obtener conexión a la base de datos
     */
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
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(array(
                "error" => true,
                "message" => "Error de conexión a la base de datos",
                "details" => $e->getMessage()
            ));
            exit();
        }

        return $this->conn;
    }
}
?>