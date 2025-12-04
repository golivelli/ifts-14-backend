<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, DELETE, PUT, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

// Validar datos requeridos
if (empty($data->carrera) || empty($data->materia) || empty($data->dia)) {
    http_response_code(400);
    echo json_encode(array(
        "error" => true,
        "message" => "Carrera, materia y día son requeridos"
    ));
    exit();
}

try {
    // 1. Parsear año y división del campo anio (ej: "1° Año - 1° División")
    preg_match('/(\d+)° Año/', $data->anio, $anio_match);
    preg_match('/(\d+)° División/', $data->anio, $div_match);
    
    $anio_num = isset($anio_match[1]) ? (int)$anio_match[1] : 1;
    $division_num = isset($div_match[1]) ? (int)$div_match[1] : null;
    
    // 2. Buscar id_carrera
    $carrera_map = [
        'Sistemas Embebidos e IoT' => 1,
        'sistemas' => 1,
        'Eficiencia Energética' => 2,
        'eficiencia' => 2
    ];
    $id_carrera = $carrera_map[$data->carrera] ?? 1;
    
    // 3. Buscar o crear la materia
    $query_materia = "SELECT id_materia FROM Materias 
                     WHERE nombre = :nombre 
                     AND anio = :anio 
                     AND id_carrera = :id_carrera
                     AND (division = :division OR (division IS NULL AND :division IS NULL))
                     LIMIT 1";
    
    $stmt_materia = $db->prepare($query_materia);
    $stmt_materia->bindParam(":nombre", $data->materia);
    $stmt_materia->bindParam(":anio", $anio_num);
    $stmt_materia->bindParam(":id_carrera", $id_carrera);
    $stmt_materia->bindParam(":division", $division_num);
    $stmt_materia->execute();
    
    if ($stmt_materia->rowCount() > 0) {
        $id_materia = $stmt_materia->fetch(PDO::FETCH_ASSOC)['id_materia'];
    } else {
        // Crear nueva materia
        $insert_materia = "INSERT INTO Materias (nombre, anio, division, id_carrera) 
                          VALUES (:nombre, :anio, :division, :id_carrera)";
        $stmt_insert = $db->prepare($insert_materia);
        $stmt_insert->bindParam(":nombre", $data->materia);
        $stmt_insert->bindParam(":anio", $anio_num);
        $stmt_insert->bindParam(":division", $division_num);
        $stmt_insert->bindParam(":id_carrera", $id_carrera);
        $stmt_insert->execute();
        $id_materia = $db->lastInsertId();
    }
    
    // 4. Buscar o crear el profesor
    $query_profesor = "SELECT id_profesor FROM Profesores WHERE nombre_completo = :nombre LIMIT 1";
    $stmt_profesor = $db->prepare($query_profesor);
    $stmt_profesor->bindParam(":nombre", $data->profesor);
    $stmt_profesor->execute();
    
    if ($stmt_profesor->rowCount() > 0) {
        $id_profesor = $stmt_profesor->fetch(PDO::FETCH_ASSOC)['id_profesor'];
    } else {
        // Crear nuevo profesor
        $insert_profesor = "INSERT INTO Profesores (nombre_completo) VALUES (:nombre)";
        $stmt_insert_prof = $db->prepare($insert_profesor);
        $stmt_insert_prof->bindParam(":nombre", $data->profesor);
        $stmt_insert_prof->execute();
        $id_profesor = $db->lastInsertId();
    }
    
    // 5. Parsear horario "18:00 - 22:15" a hora_inicio y hora_fin
    $horario_parts = explode(' - ', $data->horario);
    $hora_inicio = trim($horario_parts[0]) . ':00';
    $hora_fin = isset($horario_parts[1]) ? trim($horario_parts[1]) . ':00' : '22:15:00';
    
    // 6. Insertar el horario
    $query = "INSERT INTO Horarios (id_materia, id_profesor, dia_semana, hora_inicio, hora_fin) 
              VALUES (:id_materia, :id_profesor, :dia_semana, :hora_inicio, :hora_fin)";
    
    $stmt = $db->prepare($query);
    
    $stmt->bindParam(":id_materia", $id_materia);
    $stmt->bindParam(":id_profesor", $id_profesor);
    $stmt->bindParam(":dia_semana", $data->dia);
    $stmt->bindParam(":hora_inicio", $hora_inicio);
    $stmt->bindParam(":hora_fin", $hora_fin);
    
    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(array(
            "error" => false,
            "message" => "Horario creado exitosamente",
            "id" => $db->lastInsertId()
        ));
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(array(
        "error" => true,
        "message" => "Error al crear horario",
        "details" => $e->getMessage()
    ));
}
?>