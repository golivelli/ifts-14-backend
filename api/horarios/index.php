<?php
// Usar buffer de salida para evitar errores de headers
ob_start();

// --- CONFIGURACIÓN CORS ROBUSTA ---
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Max-Age: 86400");

// Lista de orígenes permitidos
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
    // Para peticiones sin origen (curl, postman, etc.)
    header("Access-Control-Allow-Origin: *");
}

// Manejar preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    ob_end_clean();
    http_response_code(200);
    exit();
}

// Configuración PHP
ini_set('display_errors', 0); // Desactivar en producción
ini_set('display_startup_errors', 0);
error_reporting(0); // Desactivar en producción

// --- CÓDIGO DE LA API ---
require_once dirname(__DIR__) . '/config/database.php';

header("Content-Type: application/json; charset=UTF-8");

try {
    $database = new Database();
    $db = $database->getConnection();

    $carrera = isset($_GET['carrera']) ? $_GET['carrera'] : null;
    $anio = isset($_GET['anio']) ? $_GET['anio'] : null;

    // Mapeo de slugs de carrera a IDs
    $carrera_map = [
        'sistemas' => 1,
        'eficiencia' => 2
    ];

    // Si envían el slug, lo convertimos a ID
    if ($carrera && isset($carrera_map[$carrera])) {
        $carrera_id = $carrera_map[$carrera];
    } else {
        $carrera_id = $carrera;
    }

    $query = "SELECT 
                h.id_horario,
                c.nombre as carrera_nombre,
                m.anio,
                m.division,
                m.nombre as materia_nombre,
                h.dia_semana,
                TIME_FORMAT(h.hora_inicio, '%H:%i') as hora_inicio,
                TIME_FORMAT(h.hora_fin, '%H:%i') as hora_fin,
                p.nombre_completo as profesor_nombre
              FROM Horarios h
              JOIN Materias m ON h.id_materia = m.id_materia
              JOIN Profesores p ON h.id_profesor = p.id_profesor
              JOIN Carreras c ON m.id_carrera = c.id_carrera
              WHERE 1=1";

    if ($carrera_id) {
        $query .= " AND c.id_carrera = :carrera_id";
    }
    if ($anio) {
        $query .= " AND m.anio = :anio";
    }

    $query .= " ORDER BY 
                m.anio ASC, 
                m.division ASC,
                FIELD(h.dia_semana, 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'),
                h.hora_inicio ASC";

    $stmt = $db->prepare($query);

    if ($carrera_id) {
        $stmt->bindParam(":carrera_id", $carrera_id);
    }
    if ($anio) {
        $stmt->bindParam(":anio", $anio);
    }

    $stmt->execute();
    $horarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response = array_map(function ($item) {
        $horario_str = $item['hora_inicio'] . ' - ' . $item['hora_fin'];
        $anio_str = $item['anio'] . '° Año';
        if ($item['division']) {
            $anio_str .= ' - ' . $item['division'] . '° División';
        }

        return [
            'id' => $item['id_horario'],
            'carrera' => $item['carrera_nombre'],
            'anio_division' => $anio_str,
            'materia' => $item['materia_nombre'],
            'dia' => $item['dia_semana'],
            'horario' => $horario_str,
            'profesor' => $item['profesor_nombre']
        ];
    }, $horarios);

    // Limpiar buffer y enviar respuesta
    ob_end_clean();
    http_response_code(200);
    echo json_encode($response, JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode(array(
        "error" => true,
        "message" => "Error en el servidor",
        "details" => $e->getMessage()
    ), JSON_UNESCAPED_UNICODE);
}