<?php
/**
 * API: Obtener un anuncio específico
 * GET /api/anuncios/get.php?id=X
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

$query = "SELECT * FROM anuncios WHERE id = :id";

try {
    $stmt = $db->prepare($query);
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    $anuncio = $stmt->fetch();

    if ($anuncio) {
        http_response_code(200);
        echo json_encode($anuncio);
    } else {
        http_response_code(404);
        echo json_encode(array(
            "error" => true,
            "message" => "Anuncio no encontrado"
        ));
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(array(
        "error" => true,
        "message" => "Error al obtener anuncio",
        "details" => $e->getMessage()
    ));
}
?>