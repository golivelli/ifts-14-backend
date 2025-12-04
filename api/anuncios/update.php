<?php
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: PUT, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

header("Content-Type: application/json; charset=UTF-8");

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data || !isset($data['id'])) {
        throw new Exception("Datos inválidos");
    }

    $query = "UPDATE anuncios SET
                titulo = :titulo,
                contenido = :contenido,
                imagen_url = :imagen_url,
                estado = :estado,
                id_carrera = :id_carrera,
                destacado = :destacado
              WHERE id = :id";

    $stmt = $db->prepare($query);

    $stmt->bindParam(":id", $data['id'], PDO::PARAM_INT);
    $stmt->bindParam(":titulo", $data['titulo']);
    $stmt->bindParam(":contenido", $data['contenido']);
    $stmt->bindParam(":imagen_url", $data['imagen_url']);
    $stmt->bindParam(":estado", $data['estado']);
    $stmt->bindParam(":id_carrera", $data['id_carrera'], PDO::PARAM_INT);
    $stmt->bindParam(":destacado", $data['destacado'], PDO::PARAM_INT);

    $stmt->execute();

    echo json_encode(["success" => true]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "error" => true,
        "message" => "Error al actualizar anuncio",
        "details" => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

