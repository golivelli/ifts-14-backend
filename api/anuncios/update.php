<?php
/**
 * API: Actualizar anuncio existente
 * PUT /api/anuncios/update.php
 * 
 * Body (JSON):
 * {
 *   "id": 1,
 *   "titulo": "Título actualizado",
 *   "contenido": "Contenido actualizado",
 *   "estado": "publicado",
 *   "destacado": 1
 * }
 */

require_once '../config/database.php';

header("Content-Type: application/json; charset=UTF-8");

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if (empty($data->id)) {
    http_response_code(400);
    echo json_encode(array(
        "error" => true,
        "message" => "ID es requerido"
    ));
    exit();
}

// Construir query dinámicamente según campos presentes
$fields = array();
$params = array(':id' => $data->id);

if (isset($data->titulo)) {
    $fields[] = "titulo = :titulo";
    $params[':titulo'] = $data->titulo;
}
if (isset($data->contenido)) {
    $fields[] = "contenido = :contenido";
    $params[':contenido'] = $data->contenido;
}
if (isset($data->estado)) {
    $fields[] = "estado = :estado";
    $params[':estado'] = $data->estado;
}
if (isset($data->destacado)) {
    $fields[] = "destacado = :destacado";
    $params[':destacado'] = $data->destacado;
}
if (isset($data->imagen_url)) {
    $fields[] = "imagen_url = :imagen_url";
    $params[':imagen_url'] = $data->imagen_url;
}

if (empty($fields)) {
    http_response_code(400);
    echo json_encode(array(
        "error" => true,
        "message" => "No hay campos para actualizar"
    ));
    exit();
}

$query = "UPDATE anuncios SET " . implode(", ", $fields) . " WHERE id = :id";

try {
    $stmt = $db->prepare($query);

    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(array(
            "error" => false,
            "message" => "Anuncio actualizado exitosamente"
        ));
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(array(
        "error" => true,
        "message" => "Error al actualizar anuncio",
        "details" => $e->getMessage()
    ));
}
?>