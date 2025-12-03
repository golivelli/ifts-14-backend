-- ============================================
-- SCRIPT DE MIGRACIÓN - IFTS 14 Panel Admin
-- ============================================
-- Ejecutar este script en cPanel → phpMyAdmin
-- Base de datos: ifts14c8_db
-- ============================================

-- Tabla de anuncios/noticias
CREATE TABLE IF NOT EXISTS anuncios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(255) NOT NULL,
  contenido TEXT NOT NULL,
  imagen_url VARCHAR(500),
  fecha_publicacion DATETIME DEFAULT CURRENT_TIMESTAMP,
  fecha_modificacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  estado ENUM('borrador', 'publicado', 'archivado') DEFAULT 'borrador',
  autor VARCHAR(100) DEFAULT 'Admin',
  destacado TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_estado (estado),
  INDEX idx_fecha_publicacion (fecha_publicacion),
  INDEX idx_destacado (destacado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de horarios (mejorada)
CREATE TABLE IF NOT EXISTS horarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  carrera ENUM('sistemas', 'eficiencia') NOT NULL,
  anio VARCHAR(20) NOT NULL,
  materia VARCHAR(200) NOT NULL,
  dia ENUM('Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado') NOT NULL,
  horario VARCHAR(50) NOT NULL,
  profesor VARCHAR(150),
  aula VARCHAR(50),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_carrera (carrera),
  INDEX idx_anio (anio),
  INDEX idx_dia (dia)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar algunos datos de ejemplo para horarios (opcional)
-- Puedes eliminar esto si ya tienes datos
INSERT INTO horarios (carrera, anio, materia, dia, horario, profesor, aula) VALUES
('sistemas', '1° Año', 'Matemática I', 'Lunes', '18:00 - 20:00', 'Prof. García', 'Aula 101'),
('sistemas', '1° Año', 'Programación I', 'Martes', '18:00 - 21:00', 'Prof. López', 'Aula 102'),
('eficiencia', '1° Año', 'Física I', 'Miércoles', '18:00 - 20:00', 'Prof. Martínez', 'Aula 201');

-- Insertar un anuncio de ejemplo
INSERT INTO anuncios (titulo, contenido, estado, destacado) VALUES
('Bienvenidos al Panel de Administración', 'Este es un anuncio de prueba. Puedes editarlo o eliminarlo desde el panel.', 'publicado', 1);

-- Verificar que las tablas se crearon correctamente
SELECT 'Tablas creadas exitosamente' AS mensaje;
