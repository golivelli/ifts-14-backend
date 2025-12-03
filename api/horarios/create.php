<?php
/**
 * API: Crear nuevo horario
 * POST /api/horarios/create.php
 * 
 * Body (JSON):
 * {
 *   "carrera": "sistemas|eficiencia",
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

// Validar datos requeridos
if (empty($data->carrera) || empty($data->materia) || empty($data->dia)) {
    http_response_code(400);
    echo json_encode(array(
        "error" => true,
        "message" => "Carrera, materia y día son requeridos"
    ));
    exit();
}

$query = "INSERT INTO horarios (carrera, anio, materia, dia, horario, profesor, aula) 
          VALUES (:carrera, :anio, :materia, :dia, :horario, :profesor, :aula)";

try {
    $stmt = $db->prepare($query);

    $stmt->bindParam(":carrera", $data->carrera);
    $stmt->bindParam(":anio", $data->anio);
    $stmt->bindParam(":materia", $data->materia);
    $stmt->bindParam(":dia", $data->dia);
    $stmt->bindParam(":horario", $data->horario);
    $stmt->bindParam(":profesor", $data->profesor);
    $stmt->bindParam(":aula", $data->aula);

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(array(
            "error" => false,
            "message" => "Horario creado exitosamente",
            "id" => $db->lastInsertId()
        ));
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(array(
        "error" => true,
        "message" => "Error al crear horario",
        "details" => $e->getMessage()
    ));
}
?>