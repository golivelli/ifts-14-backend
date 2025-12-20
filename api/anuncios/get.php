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
header("Access-Control-Allow-Methods: GET, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

header("Content-Type: application/json; charset=UTF-8");

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if ($id <= 0) {
        throw new Exception("ID invů˝lido");
    }

    $stmt = $db->prepare("SELECT * FROM anuncios WHERE id = :id LIMIT 1");
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    $anuncio = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($anuncio ?: []);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "error" => true,
        "message" => "Error al obtener anuncio",
        "details" => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

