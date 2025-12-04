<?php
// ---------------------------
// CORS (OBLIGATORIO en cPanel)
// ---------------------------
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

header("Content-Type: application/json; charset=UTF-8");

// ---------------------------
// Cargar base de datos (ruta absoluta)
// ---------------------------
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    if (!$db) {
        throw new Exception("No se pudo conectar a la base de datos");
    }

    // Obtener filtros
    $estado = $_GET['estado'] ?? null;
    $destacado = $_GET['destacado'] ?? null;
    $id_carrera = isset($_GET['id_carrera']) ? (int) $_GET['id_carrera'] : null;
    $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : null;

    $query = "SELECT 
                id, 
                id_carrera,
                titulo, 
                contenido, 
                imagen_url,
                fecha_publicacion,
                fecha_modificacion,
                estado, 
                autor, 
                destacado,
                created_at,
                updated_at
              FROM anuncios 
              WHERE 1=1";

    if ($estado) $query .= " AND estado = :estado";
    if ($destacado !== null) $query .= " AND destacado = :destacado";
    if ($id_carrera) $query .= " AND id_carrera = :id_carrera";

    $query .= " ORDER BY fecha_publicacion DESC";

    if ($limit && $limit > 0) {
        $query .= " LIMIT :limit";
    }

    $stmt = $db->prepare($query);

    if ($estado) $stmt->bindParam(":estado", $estado);
    if ($destacado !== null) $stmt->bindParam(":destacado", $destacado, PDO::PARAM_INT);
    if ($id_carrera) $stmt->bindParam(":id_carrera", $id_carrera, PDO::PARAM_INT);
    if ($limit && $limit > 0) $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);

    $stmt->execute();
    $anuncios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($anuncios, JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "error" => true,
        "message" => "Error al obtener anuncios",
        "details" => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
