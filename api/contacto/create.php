<?php
ob_start();

header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: POST, OPTIONS");
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

    $data = json_decode(file_get_contents("php://input"), true);

    $nombre = trim($data['nombre'] ?? '');
    $email = trim($data['email'] ?? '');
    $telefono = trim($data['telefono'] ?? '');
    $motivo = trim($data['motivo'] ?? 'Consulta general');
    $mensaje = trim($data['mensaje'] ?? '');

    if (empty($nombre) || empty($email) || empty($mensaje)) {
        ob_end_clean();
        http_response_code(422);
        echo json_encode([
            "error" => true,
            "message" => "Nombre, correo y mensaje son obligatorios."
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }

    $query = "INSERT INTO contact_messages (nombre, email, telefono, motivo, mensaje)
              VALUES (:nombre, :email, :telefono, :motivo, :mensaje)";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':telefono', $telefono);
    $stmt->bindParam(':motivo', $motivo);
    $stmt->bindParam(':mensaje', $mensaje);
    $stmt->execute();

    ob_end_clean();
    http_response_code(201);
    echo json_encode([
        "error" => false,
        "message" => "Consulta registrada correctamente."
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        "error" => true,
        "message" => "No se pudo registrar la consulta.",
        "details" => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
