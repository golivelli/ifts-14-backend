<?php
ob_start();

header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Max-Age: 86400");

$allowed_origins = [
    'http://localhost:4200',
    'http://localhost',
    'https://www.ifts14.com.ar',
    'https://ifts14.com.ar'
];

$http_origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (in_array($http_origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: " . $http_origin);
} else if (empty($http_origin)) {
    header("Access-Control-Allow-Origin: *");
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    ob_end_clean();
    http_response_code(200);
    exit();
}

require_once dirname(__DIR__) . '/config/database.php';

header("Content-Type: application/json; charset=UTF-8");

try {
    $database = new Database();
    $db = $database->getConnection();

    $query = "SELECT id, nombre, email, telefono, motivo, mensaje, created_at
              FROM contact_messages
              ORDER BY created_at DESC";

    $stmt = $db->prepare($query);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    ob_end_clean();
    http_response_code(200);
    echo json_encode($rows, JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        "error" => true,
        "message" => "Error al obtener las consultas.",
        "details" => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
