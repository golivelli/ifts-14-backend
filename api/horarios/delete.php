<?php
/**
 * API: Eliminar horario
 * DELETE /api/horarios/delete.php?id=X
 */

require_once '../config/database.php';

header("Content-Type: application/json; charset=UTF-8");

$database = new Database();
$db = $database->getConnection();

$id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$id) {
    http_response_code(400);
    echo json_encode(array(
        "error" => true,
        "message" => "ID es requerido"
    ));
    exit();
}

$query = "DELETE FROM horarios WHERE id = :id";

try {
    $stmt = $db->prepare($query);
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);

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