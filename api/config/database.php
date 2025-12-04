<?php
class Database
{
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

        } catch (PDOException $e) {
            // Nunca exponer errores sensibles en producción
            http_response_code(500);
            echo json_encode(["error" => "Error de conexión a la base de datos"]);
            exit();
        }

        return $this->conn;
    }
}
?>