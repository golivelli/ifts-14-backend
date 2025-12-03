<?php
/**
 * API: Listar todos los anuncios
 * GET /api/anuncios/
 * 
 * Parámetros opcionales:
 * - estado: borrador|publicado|archivado
 * - destacado: 0|1
 */

require_once '../config/database.php';

header("Content-Type: application/json; charset=UTF-8");

$database = new Database();
$db = $database->getConnection();

// Obtener parámetros de filtro
$estado = isset($_GET['estado']) ? $_GET['estado'] : null;
$destacado = isset($_GET['destacado']) ? $_GET['destacado'] : null;

// Construir query
$query = "SELECT * FROM anuncios WHERE 1=1";

if ($estado) {
    $query .= " AND estado = :estado";
}
if ($destacado !== null) {
    $query .= " AND destacado = :destacado";
}

$query .= " ORDER BY fecha_publicacion DESC";

try {
    $stmt = $db->prepare($query);

    if ($estado) {
        $stmt->bindParam(":estado", $estado);
    }
    if ($destacado !== null) {
        $stmt->bindParam(":destacado", $destacado, PDO::PARAM_INT);
    }

    $stmt->execute();
    $anuncios = $stmt->fetchAll();

    http_response_code(200);
    echo json_encode($anuncios);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(array(
        "error" => true,
        "message" => "Error al obtener anuncios",
        "details" => $e->getMessage()
    ));
}
?>