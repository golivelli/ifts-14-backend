<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../db.php';

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if ($data === null) {
    http_response_code(400);
    echo json_encode(['ok'=>false,'error'=>'json invalido','raw'=>$raw]);
    exit;
}

$id = isset($data['id']) ? (int)$data['id'] : 0;
$tipo = $data['tipo'] ?? null;
$titulo = $data['titulo'] ?? null;
$descripcion = $data['descripcion'] ?? null;
$fecha = $data['fecha'] ?? null;
$termina = $data['termina'] ?? null;
$tecnicatura_id = isset($data['tecnicatura_id']) ? (int)$data['tecnicatura_id'] : 0;
$status = $data['status'] ?? 'publicado';
$file_path = $data['file_path'] ?? null;

if ($id <= 0 || !$tipo || !$titulo || !$descripcion || !$fecha || $tecnicatura_id <= 0) {
    http_response_code(400);
    echo json_encode(['ok'=>false,'error'=>'faltan campos']);
    exit;
}

$sql = "
UPDATE posts SET tipo=?, titulo=?, descripcion=?, fecha=?, termina=?,
tecnicatura_id=?, status=?, file_path=? WHERE id=?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['ok'=>false,'error'=>$conn->error]);
    exit;
}

$stmt->bind_param("sssssissi", $tipo, $titulo, $descripcion, $fecha, $termina,
    $tecnicatura_id, $status, $file_path, $id);

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(['ok'=>false,'error'=>$stmt->error]);
    exit;
}

if ($stmt->affected_rows === 0) {
    echo json_encode(['ok'=>false,'msg'=>'no hay o no hay cambios','id'=>$id]);
} else {
    echo json_encode(['ok'=>true,'msg'=>'actualizado','id'=>$id]);
}

$stmt->close();
$conn->close();
