<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, DELETE, PUT, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Obtener ID desde query parameter
$id = isset($_GET['id']) ? $_GET['id'] : null;

if (empty($id)) {
    http_response_code(400);
    echo json_encode(array(
        "error" => true,
        "message" => "ID es requerido"
    ));
    exit();
}

try {
    // Eliminar el horario de la tabla Horarios
    $query = "DELETE FROM Horarios WHERE id_horario = :id";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(":id", $id);
    
    if ($stmt->execute()) {
        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(array(
                "error" => false,
                "message" => "Horario eliminado exitosamente"
            ));
        } else {
            http_response_code(404);
            echo json_encode(array(
                "error" => true,
                "message" => "Horario no encontrado"
            ));
        }
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(array(
        "error" => true,
        "message" => "Error al eliminar horario",
        "details" => $e->getMessage()
    ));
}
?>