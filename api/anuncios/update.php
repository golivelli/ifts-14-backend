<?php
ob_start();

header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: PUT, OPTIONS");

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

header("Content-Type: application/json; charset=UTF-8");

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data || !isset($data['id'])) {
        throw new Exception("Datos invalidos");
    }

    $stmt = $db->prepare("SELECT * FROM anuncios WHERE id = :id LIMIT 1");
    $stmt->bindParam(':id', $data['id'], PDO::PARAM_INT);
    $stmt->execute();
    $anuncio = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$anuncio) {
        throw new Exception("Anuncio no encontrado");
    }

    $titulo = isset($data['titulo']) ? trim($data['titulo']) : $anuncio['titulo'];
    $contenido = isset($data['contenido']) ? trim($data['contenido']) : $anuncio['contenido'];
    $estado = isset($data['estado']) ? $data['estado'] : $anuncio['estado'];
    $destacado = isset($data['destacado']) ? (int)$data['destacado'] : (int)$anuncio['destacado'];
    $imagenUrl = array_key_exists('imagen_url', $data) ? $data['imagen_url'] : $anuncio['imagen_url'];
    $idCarrera = array_key_exists('id_carrera', $data) ? $data['id_carrera'] : $anuncio['id_carrera'];
    $autor = array_key_exists('autor', $data) ? trim($data['autor']) : $anuncio['autor'];

    $estados_validos = ['borrador', 'publicado', 'archivado'];
    if (!in_array($estado, $estados_validos, true)) {
        $estado = $anuncio['estado'];
    }
    if ($autor === '') {
        $autor = $anuncio['autor'];
    }

    $query = "UPDATE anuncios SET
                titulo = :titulo,
                contenido = :contenido,
                imagen_url = :imagen_url,
                estado = :estado,
                id_carrera = :id_carrera,
                destacado = :destacado,
                autor = :autor
              WHERE id = :id";

    $stmt = $db->prepare($query);

    $stmt->bindParam(":id", $data['id'], PDO::PARAM_INT);
    $stmt->bindParam(":titulo", $titulo);
    $stmt->bindParam(":contenido", $contenido);
    $stmt->bindParam(":estado", $estado);
    $stmt->bindParam(":destacado", $destacado, PDO::PARAM_INT);
    $stmt->bindParam(":autor", $autor);

    if ($imagenUrl === null || $imagenUrl === '') {
        $stmt->bindValue(":imagen_url", null, PDO::PARAM_NULL);
    } else {
        $stmt->bindParam(":imagen_url", $imagenUrl);
    }

    if ($idCarrera === null || $idCarrera === '') {
        $stmt->bindValue(":id_carrera", null, PDO::PARAM_NULL);
    } else {
        $idCarreraInt = (int)$idCarrera;
        $stmt->bindParam(":id_carrera", $idCarreraInt, PDO::PARAM_INT);
    }

    $stmt->execute();

    ob_end_clean();
    echo json_encode(["success" => true]);

} catch (Exception $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        "error" => true,
        "message" => "Error al actualizar anuncio",
        "details" => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
