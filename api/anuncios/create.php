<?php
/**
 * API: Crear nuevo anuncio
 * POST /api/anuncios/create.php
 * 
 * Body (JSON):
 * {
 *   "titulo": "Título del anuncio",
 *   "contenido": "Contenido del anuncio",
 *   "estado": "borrador|publicado|archivado",
 *   "destacado": 0|1,
 *   "autor": "Nombre del autor"
 * }
 */

require_once '../config/database.php';

header("Content-Type: application/json; charset=UTF-8");

$database = new Database();
$db = $database->getConnection();

// Obtener datos del POST
$data = json_decode(file_get_contents("php://input"));

// Validar datos requeridos
if (empty($data->titulo) || empty($data->contenido)) {
    http_response_code(400);
    echo json_encode(array(
        "error" => true,
        "message" => "Título y contenido son requeridos"
    ));
    exit();
}

// Valores por defecto
$estado = isset($data->estado) ? $data->estado : 'borrador';
$autor = isset($data->autor) ? $data->autor : 'Admin';
$destacado = isset($data->destacado) ? $data->destacado : 0;
$imagen_url = isset($data->imagen_url) ? $data->imagen_url : null;

$query = "INSERT INTO anuncios (titulo, contenido, imagen_url, estado, autor, destacado) 
          VALUES (:titulo, :contenido, :imagen_url, :estado, :autor, :destacado)";

try {
    $stmt = $db->prepare($query);

    $stmt->bindParam(":titulo", $data->titulo);
    $stmt->bindParam(":contenido", $data->contenido);
    $stmt->bindParam(":imagen_url", $imagen_url);
    $stmt->bindParam(":estado", $estado);
    $stmt->bindParam(":autor", $autor);
    $stmt->bindParam(":destacado", $destacado, PDO::PARAM_INT);

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(array(
            "error" => false,
            "message" => "Anuncio creado exitosamente",
            "id" => $db->lastInsertId()
        ));
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(array(
        "error" => true,
        "message" => "Error al crear anuncio",
        "details" => $e->getMessage()
    ));
}
?>