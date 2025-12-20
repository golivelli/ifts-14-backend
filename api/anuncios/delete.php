<?php
$allowed_origins = [
    'https://ifts14.com.ar',
    'https://www.ifts14.com.ar',
    'http://localhost:4200',
    'http://localhost'
];

$http_origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($http_origin, $allowed_origins, true)) {
    header("Access-Control-Allow-Origin: " . $http_origin);
}

header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: DELETE, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

header("Content-Type: application/json; charset=UTF-8");

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data || !isset($data['id'])) {
        throw new Exception("ID invǭlido");
    }

    $stmt = $db->prepare("DELETE FROM anuncios WHERE id = :id");
    $stmt->bindParam(":id", $data['id'], PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode(["success" => true]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "error" => true,
        "message" => "Error al eliminar anuncio",
        "details" => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
