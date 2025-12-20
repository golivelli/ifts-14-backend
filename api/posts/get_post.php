<?php
header('Content-Type: application/json; charset=utf-8');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../db.php';

$conn->set_charset('utf8mb4');

$sql = "
    SELECT 
        p.id      AS profesor_id,
        p.nombre  AS profesor,
        m.id      AS materia_id,
        m.nombre  AS materia,
        m.dia,
        m.franja_horaria,
        m.anio
    FROM profesores p
    LEFT JOIN materias m ON m.profesor_id = p.id
    ORDER BY p.nombre, m.anio, m.dia, m.franja_horaria
";

$result = $conn->query($sql);

if (!$result) {
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'error' => 'error en la consulta: ' . $conn->error
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$filas = [];

while ($row = $result->fetch_assoc()) {
    $filas[] = $row;
}

echo json_encode([
    'ok'   => true,
    'data' => $filas
], JSON_UNESCAPED_UNICODE);

$conn->close();
