<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../db.php';

$conn->set_charset('utf8mb4');

if ($conn->connect_error) {
    echo json_encode(['ok'=>false,'error'=>'conn '.$conn->connect_error]);
    exit;
}

$sql = "SELECT * FROM materias";

$result = $conn->query($sql);

if (!$result) {
    echo json_encode(['ok'=>false,'error'=>'sql '.$conn->error]);
    exit;
}

$rows = [];
while ($r = $result->fetch_assoc()) {
    $rows[] = $r;
}

echo json_encode($rows, JSON_UNESCAPED_UNICODE);
