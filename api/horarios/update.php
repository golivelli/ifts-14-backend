<?php
/**
 * API: Actualizar horario existente
 * PUT /api/horarios/update.php
 * 
 * Body (JSON):
 * {
 *   "id": 1,
 *   "carrera": "sistemas",
 *   "anio": "1° Año",
 *   "materia": "Matemática I",
 *   "dia": "Lunes",
 *   "horario": "18:00 - 20:00",
 *   "profesor": "Prof. García",
 *   "aula": "Aula 101"
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

// Construir query dinámicamente
$fields = array();
$params = array(':id' => $data->id);

if (isset($data->carrera)) {
    $fields[] = "carrera = :carrera";
    $params[':carrera'] = $data->carrera;
}
if (isset($data->anio)) {
    $fields[] = "anio = :anio";
    $params[':anio'] = $data->anio;
}
if (isset($data->materia)) {
    $fields[] = "materia = :materia";
    $params[':materia'] = $data->materia;
}
if (isset($data->dia)) {
    $fields[] = "dia = :dia";
    $params[':dia'] = $data->dia;
}
if (isset($data->horario)) {
    $fields[] = "horario = :horario";
    $params[':horario'] = $data->horario;
}
if (isset($data->profesor)) {
    $fields[] = "profesor = :profesor";
    $params[':profesor'] = $data->profesor;
}
if (isset($data->aula)) {
    $fields[] = "aula = :aula";
    $params[':aula'] = $data->aula;
}

if (empty($fields)) {
    http_response_code(400);
    echo json_encode(array(
        "error" => true,
        "message" => "No hay campos para actualizar"
    ));
    exit();
}

$query = "UPDATE horarios SET " . implode(", ", $fields) . " WHERE id = :id";

try {
    $stmt = $db->prepare($query);

    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(array(
            "error" => false,
            "message" => "Horario actualizado exitosamente"
        ));
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(array(
        "error" => true,
        "message" => "Error al actualizar horario",
        "details" => $e->getMessage()
    ));
}
?>