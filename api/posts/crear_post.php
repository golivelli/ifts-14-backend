<?php


require_once __DIR__ . '/../db.php';

header('Content-Type: application/json; charset=utf-8');

 
 


$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'JSON inválido', 'raw' => $raw]);
    exit;
}

$tipo           = $data['tipo']           ?? null;
$titulo         = $data['titulo']         ?? null;
$descripcion    = $data['descripcion']    ?? null;
$fecha          = $data['fecha']          ?? null;  
$termina        = $data['termina']        ?? null;  
$tecnicatura_id = $data['tecnicatura_id'] ?? null;  
$file_path      = $data['file_path']      ?? null;  


if (!$tipo || !$titulo || !$descripcion || !$fecha || !$tecnicatura_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Faltan campos obligatorios']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO posts (tipo, titulo, descripcion, fecha, termina, tecnicatura_id, file_path)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en prepare', 'detalle' => $conn->error]);
    exit;
}

$stmt->bind_param(
    "ssssiss",
    $tipo,
    $titulo,
    $descripcion,
    $fecha,
    $termina,
    $tecnicatura_id,
    $file_path
);


if ($stmt->execute()) {
    http_response_code(201);
    echo json_encode([
        'ok'  => true,
        'id'  => $conn->insert_id
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'ok'    => false,
        'error' => $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>
