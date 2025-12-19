<?php
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: DELETE, OPTIONS");

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
    http_response_code(200);
    exit();
}

header("Content-Type: application/json; charset=UTF-8");

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/config/database.php';

try {
    // Conexion
    $database = new Database();
    $db = $database->getConnection();

    $raw = file_get_contents("php://input");
    $data = json_decode($raw, true);

    $id = null;
    if (isset($data['id'])) {
        $id = $data['id'];
    } else if (isset($_GET['id'])) {
        $id = $_GET['id'];
    } else if (isset($_POST['id'])) {
        $id = $_POST['id'];
    }

    if ($id === null) {
        throw new Exception("ID requerido");
    }

    $id = (int) $id;
    if ($id <= 0) {
        throw new Exception("ID invalido");
    }

    $stmt = $db->prepare("DELETE FROM contact_messages WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode(["success" => true]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "error" => true,
        "message" => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
