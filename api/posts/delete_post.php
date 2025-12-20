<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../db.php';

$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);

$id = 0;

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
} elseif (is_array($data) && isset($data['id'])) {
    $id = (int)$data['id'];
}

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['ok'=>false,'error'=>'falta id']);
    exit;
}

$check = $conn->prepare("SELECT id FROM posts WHERE id = ?");
$check->bind_param("i", $id);
$check->execute();
$check->store_result();

if ($check->num_rows === 0) {
    echo json_encode(['ok'=>false,'msg'=>'no existe ese id','id'=>$id]);
    $check->close();
    $conn->close();
    exit;
}

$check->close();

$stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['ok'=>false,'error'=>$conn->error]);
    $conn->close();
    exit;
}

$stmt->bind_param("i", $id);

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(['ok'=>false,'error'=>$stmt->error]);
    $stmt->close();
    $conn->close();
    exit;
}

if ($stmt->affected_rows === 0) {
    echo json_encode(['ok'=>false,'msg'=>'no se borro ninguna fila','id'=>$id]);
} else {
    echo json_encode(['ok'=>true,'msg'=>'eliminado','id'=>$id]);
}

$stmt->close();
$conn->close();
