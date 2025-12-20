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
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=utf-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if (isset($_GET['debug'])) {
    echo json_encode([
        "raw"   => file_get_contents("php://input"),
        "json"  => json_decode(file_get_contents("php://input"), true),
        "method" => $_SERVER['REQUEST_METHOD']
    ], JSON_PRETTY_PRINT);
    exit();
}

require_once __DIR__ . "/../config/database.php";

try {
    $database = new Database();
    $db = $database->getConnection();

    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data) {
        throw new Exception("JSON invalido o vacio");
    }

    $titulo     = $data["titulo"]     ?? null;
    $contenido  = $data["contenido"]  ?? null;
    $imagen_url = $data["imagen_url"] ?? "";
    $destacado  = isset($data["destacado"]) ? (int)$data["destacado"] : 0;
    $id_carrera = isset($data["id_carrera"]) ? (int)$data["id_carrera"] : 1;
    $estado     = $data["estado"]     ?? "borrador";
    $autor      = trim($data["autor"] ?? "Admin");

    if (!$titulo || !$contenido) {
        throw new Exception("Faltan campos obligatorios: titulo o contenido");
    }

    $estados_validos = ['borrador', 'publicado', 'archivado'];
    if (!in_array($estado, $estados_validos, true)) {
        $estado = 'borrador';
    }
    if ($autor === '') {
        $autor = 'Admin';
    }

    $query = "INSERT INTO anuncios 
                (titulo, contenido, imagen_url, destacado, estado, autor, id_carrera, fecha_publicacion)
              VALUES 
                (:titulo, :contenido, :imagen_url, :destacado, :estado, :autor, :id_carrera, NOW())";

    $stmt = $db->prepare($query);

    $stmt->bindParam(":titulo", $titulo);
    $stmt->bindParam(":contenido", $contenido);
    $stmt->bindParam(":imagen_url", $imagen_url);
    $stmt->bindParam(":destacado", $destacado, PDO::PARAM_INT);
    $stmt->bindParam(":estado", $estado);
    $stmt->bindParam(":autor", $autor);
    $stmt->bindParam(":id_carrera", $id_carrera, PDO::PARAM_INT);

    $stmt->execute();

    http_response_code(200);
    echo json_encode([
        "success" => true,
        "message" => "Anuncio creado correctamente",
        "id" => $db->lastInsertId()
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "error" => true,
        "message" => "Error al crear anuncio",
        "details" => $e->getMessage()
    ]);
}

