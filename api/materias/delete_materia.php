<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'metodo no permitido']);
    exit;
}

require_once __DIR__ . '/../db.php';


$input = json_decode(file_get_contents('php://input'), true);
$id = $input['id'] ?? null;

if (!$id) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'falta id']);
    exit;
}

$stmt = $conn->prepare("DELETE FROM materias WHERE id = ?");
$stmt->bind_param('i', $id);

if ($stmt->execute()) {
    echo json_encode(['ok' => true]);
} else {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $stmt->error]);
}

$stmt->close();
$conn->close();
