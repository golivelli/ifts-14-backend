<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'metodo no permitido']);
    exit;
}

require_once __DIR__ . '/../db.php';

$input = json_decode(file_get_contents('php://input'), true);

$dia            = $input['dia'] ?? null;
$franja         = $input['franja_horaria'] ?? null;
$anio           = $input['anio'] ?? null;
$nombre         = $input['nombre'] ?? null;
$profesor_id    = $input['profesor_id'] ?? null;

if (!$dia || !$franja || !$anio || !$nombre || !$profesor_id) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'faltan datos']);
    exit;
}

$stmt = $conn->prepare(
    "INSERT INTO materias (dia, franja_horaria, anio, nombre, profesor_id)
     VALUES (?, ?, ?, ?, ?)"
);

$stmt->bind_param('ssisi', $dia, $franja, $anio, $nombre, $profesor_id);

if ($stmt->execute()) {
    echo json_encode([
        'ok' => true,
        'id' => $stmt->insert_id
    ]);
} else {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $stmt->error]);
}

$stmt->close();
$conn->close();
