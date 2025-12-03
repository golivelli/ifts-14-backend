<?php
/**
 * API: Listar todos los horarios
 * GET /api/horarios/
 * 
 * Parámetros opcionales:
 * - carrera: sistemas|eficiencia
 * - anio: 1° Año|2° Año|3° Año
 */

require_once '../config/database.php';

header("Content-Type: application/json; charset=UTF-8");

$database = new Database();
$db = $database->getConnection();

$carrera = isset($_GET['carrera']) ? $_GET['carrera'] : null;
$anio = isset($_GET['anio']) ? $_GET['anio'] : null;

$query = "SELECT * FROM horarios WHERE 1=1";

if ($carrera) {
    $query .= " AND carrera = :carrera";
}
if ($anio) {
    $query .= " AND anio = :anio";
}

$query .= " ORDER BY anio, dia, horario";

try {
    $stmt = $db->prepare($query);

    if ($carrera) {
        $stmt->bindParam(":carrera", $carrera);
    }
    if ($anio) {
        $stmt->bindParam(":anio", $anio);
    }

    $stmt->execute();
    $horarios = $stmt->fetchAll();

    http_response_code(200);
    echo json_encode($horarios);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(array(
        "error" => true,
        "message" => "Error al obtener horarios",
        "details" => $e->getMessage()
    ));
}
?>