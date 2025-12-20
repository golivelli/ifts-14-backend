<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'metodo no permitido']);
    exit;
}

require_once __DIR__ . '/../db.php';


$input = json_decode(file_get_contents('php://input'), true);

$id             = $input['id'] ?? null;
$dia            = $input['dia'] ?? null;
$franja         = $input['franja_horaria'] ?? null;
$anio           = $input['anio'] ?? null;
$nombre         = $input['nombre'] ?? null;
$profesor_id    = $input['profesor_id'] ?? null;

if (!$id || !$dia || !$franja || !$anio || !$nombre || !$profesor_id) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'faltan datos']);
    exit;
}

$stmt = $conn->prepare(
    "UPDATE materias
     SET dia = ?, franja_horaria = ?, anio = ?, nombre = ?, profesor_id = ?
     WHERE id = ?"
);

$stmt->bind_param('ssisii', $dia, $franja, $anio, $nombre, $profesor_id, $id);

if ($stmt->execute()) {
    echo json_encode(['ok' => true]);
} else {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $stmt->error]);
}

$stmt->close();
$conn->close();
