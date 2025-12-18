-- ==========================================
-- SCRIPT DE MIGRACIÓN - IFTS 14
-- Base de datos: ifts14c8_dev
-- ==========================================

-- ==========================================
-- 1. CREACIÓN DE TABLAS - SISTEMA DE HORARIOS
-- ==========================================

CREATE TABLE IF NOT EXISTS Carreras (
    id_carrera INT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS Profesores (
    id_profesor INT PRIMARY KEY AUTO_INCREMENT,
    nombre_completo VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS Materias (
    id_materia INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    anio INT NOT NULL, -- 1, 2, o 3
    division INT DEFAULT NULL, -- 1 o 2 (Solo aplica a 1er año Sist. Emb.)
    id_carrera INT,
    FOREIGN KEY (id_carrera) REFERENCES Carreras(id_carrera)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS Horarios (
    id_horario INT PRIMARY KEY AUTO_INCREMENT,
    id_materia INT,
    id_profesor INT,
    dia_semana VARCHAR(15), -- Lunes, Martes, etc.
    hora_inicio TIME,
    hora_fin TIME,
    FOREIGN KEY (id_materia) REFERENCES Materias(id_materia),
    FOREIGN KEY (id_profesor) REFERENCES Profesores(id_profesor)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- 2. TABLA DE ANUNCIOS (PANEL ADMIN)
-- ==========================================

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

-- ==========================================
-- 3. TABLA DE CONSULTAS DE CONTACTO
-- ==========================================

CREATE TABLE IF NOT EXISTS contact_messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL,
  telefono VARCHAR(40),
  motivo VARCHAR(100),
  mensaje TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- 4. INSERCIÓN DE DATOS BASE (Carreras y Profesores)
-- ==========================================

-- Insertar Carreras
INSERT INTO Carreras (id_carrera, nombre) VALUES 
(1, 'Sistemas Embebidos e IoT'),
(2, 'Eficiencia Energética'),
(3, 'Sin carrera específica');

-- Insertar Profesores (Lista unificada)
INSERT INTO Profesores (nombre_completo) VALUES 
('Belaunde, Victor Manuel'),    -- 1
('Bertani, Jorge'),             -- 2
('Tejerina, Sandra'),           -- 3
('Velárdez, Germán'),           -- 4
('Granzella, Damián Eduardo'),  -- 5
('Alonso Castillo, Pablo'),     -- 6
('Gómez Molino, Hernán'),       -- 7
('Prieto, Gustavo'),            -- 8
('Petroff, Maximiliano'),       -- 9
('Iaria, Pablo'),               -- 10
('Rodríguez, Daniel F.'),       -- 11
('Gómez Riera, Germán'),        -- 12
('Gallardo, Lucia'),            -- 13
('Schvartz, Sebastian'),        -- 14
('Pillon, Fernando'),          -- 15
('Pons, Flavio'),               -- 16
('Gagliardi, Adrián'),          -- 17
('López, C. Guillermo'),        -- 18
('Fuentes, Carlos A.'),         -- 19
('Belaunde, V. / Bertani, J.'), -- 20 (Cátedra compartida)
('Fuentes, C. / Pons, F. / Schvartz, S.'); -- 21 (Cátedra compartida)

-- ==========================================
-- 5. INSERCIÓN DE MATERIAS Y HORARIOS
-- ==========================================

-- CARRERA: SISTEMAS EMBEBIDOS (ID 1)
-- -----------------------------------

-- 1er Año - 1ra División
INSERT INTO Materias (nombre, anio, division, id_carrera) VALUES 
('Desarrollo de Sistemas Web', 1, 1, 1),             -- ID: 1
('Circuitos Eléctricos y Electrónicos', 1, 1, 1),    -- ID: 2
('Técnicas de Programación', 1, 1, 1),               -- ID: 3
('Electrónica Digital y Microprocesadores', 1, 1, 1);-- ID: 4

INSERT INTO Horarios (id_materia, id_profesor, dia_semana, hora_inicio, hora_fin) VALUES
(1, 1, 'Lunes', '18:00', '22:15'),     -- Des. Sist Web / Belaunde
(2, 2, 'Martes', '18:00', '22:15'),    -- Circuitos / Bertani
(3, 3, 'Miércoles', '18:00', '22:15'), -- Tec. Prog / Tejerina
(4, 20, 'Jueves', '18:00', '22:15'),   -- Elec Digital / Belaunde & Bertani
(1, 1, 'Viernes', '18:00', '20:00'),   -- Des. Sist Web (Viernes)
(3, 2, 'Viernes', '20:15', '22:15');   -- Tec. Prog (Viernes) / Bertani

-- 1er Año - 2da División
INSERT INTO Materias (nombre, anio, division, id_carrera) VALUES 
('Desarrollo y Testing de Software SE', 1, 2, 1),    -- ID: 5
('Protocolos de IoT', 1, 2, 1),                      -- ID: 6
('Programación y Comunicación en SE', 1, 2, 1),      -- ID: 7
('Administración de Base de Datos', 1, 2, 1);        -- ID: 8

INSERT INTO Horarios (id_materia, id_profesor, dia_semana, hora_inicio, hora_fin) VALUES
(5, 4, 'Lunes', '18:00', '22:15'),     -- Des y Testing / Velárdez
(6, 5, 'Martes', '18:00', '22:15'),    -- Protocolos / Granzella
(7, 6, 'Miércoles', '18:00', '22:15'), -- Prog y Com / Alonso Castillo
(8, 1, 'Jueves', '18:00', '22:15'),    -- Adm BD / Belaunde
(8, 1, 'Viernes', '20:15', '22:15');   -- Adm BD / Belaunde

-- 2do Año (Sin división especificada)
INSERT INTO Materias (nombre, anio, id_carrera) VALUES 
('Sistemas Operativos en Tiempo Real', 2, 1),        -- ID: 9
('Ciberseguridad en IoT', 2, 1),                     -- ID: 10
('Desarrollo de Aplicaciones Vinculadas a BD', 2, 1);-- ID: 11

INSERT INTO Horarios (id_materia, id_profesor, dia_semana, hora_inicio, hora_fin) VALUES
(9, 7, 'Martes', '18:00', '22:15'),    -- Sist Op / Gómez Molino
(10, 8, 'Miércoles', '18:00', '22:15'),-- Ciberseguridad / Prieto
(11, 9, 'Viernes', '18:00', '22:15');  -- Des Apps / Petroff

-- 3er Año
INSERT INTO Materias (nombre, anio, id_carrera) VALUES 
('Procesamiento de Aprendizaje Automático', 3, 1),   -- ID: 12
('Proyecto Integrador', 3, 1),                       -- ID: 13
('Modelizado y Minería de Datos', 3, 1);             -- ID: 14

INSERT INTO Horarios (id_materia, id_profesor, dia_semana, hora_inicio, hora_fin) VALUES
(12, 10, 'Lunes', '18:00', '22:15'),     -- ML / Iaria
(13, 6, 'Martes', '18:00', '22:15'),     -- Proy Int / Alonso Castillo
(12, 10, 'Miércoles', '18:00', '20:00'), -- ML / Iaria
(14, 11, 'Miércoles', '20:15', '22:15'), -- Minería / Rodríguez
(13, 6, 'Jueves', '18:40', '20:55'),     -- Proy Int / Alonso Castillo
(14, 11, 'Viernes', '18:00', '22:15');   -- Minería / Rodríguez

-- CARRERA: EFICIENCIA ENERGÉTICA (ID 2)
-- -----------------------------------

-- 1er Año
INSERT INTO Materias (nombre, anio, id_carrera) VALUES 
('Física', 1, 2),                            -- ID: 15
('Prácticas Profesionalizantes I', 1, 2),    -- ID: 16
('Representación Gráfica Específica', 1, 2), -- ID: 17
('Fuentes de Energía', 1, 2),                -- ID: 18
('Álgebra Lineal', 1, 2);                    -- ID: 19

INSERT INTO Horarios (id_materia, id_profesor, dia_semana, hora_inicio, hora_fin) VALUES
(15, 6, 'Lunes', '18:00', '20:55'),      -- Física / Alonso Castillo
(16, 12, 'Martes', '18:40', '22:15'),    -- Pract I / Gómez Riera
(17, 13, 'Miércoles', '18:00', '22:15'), -- Rep Graf / Gallardo
(18, 14, 'Jueves', '18:00', '22:15'),    -- Fuentes / Schvartz
(19, 6, 'Viernes', '18:00', '21:35');    -- Algebra / Alonso Castillo

-- 2do Año
INSERT INTO Materias (nombre, anio, id_carrera) VALUES 
('Evaluación Energética de Edificios', 2, 2),        -- ID: 20
('Prácticas Profesionalizantes II', 2, 2),           -- ID: 21
('Sistemas de Climatización', 2, 2),                 -- ID: 22
('Instalaciones Eléctricas', 2, 2),                  -- ID: 23
('Problemáticas Socio Económicas de la Energía', 2, 2); -- ID: 24

INSERT INTO Horarios (id_materia, id_profesor, dia_semana, hora_inicio, hora_fin) VALUES
(20, 14, 'Lunes', '18:00', '22:15'),     -- Eval Ener / Schvartz
(21, 15, 'Martes', '18:00', '20:55'),    -- Pract II / Pillon
(22, 16, 'Miércoles', '18:00', '22:15'), -- Clima / Pons
(23, 17, 'Jueves', '18:00', '22:15'),    -- Inst Elec / Gagliardi
(24, 18, 'Viernes', '18:00', '20:55');   -- Prob Socio / López

-- 3er Año
INSERT INTO Materias (nombre, anio, id_carrera) VALUES 
('Instalaciones Industriales', 3, 2),                -- ID: 25
('Inglés Técnico', 3, 2),                            -- ID: 26
('Instalaciones Aplicadas a Energías Renovables', 3, 2), -- ID: 27
('Gestión Energética', 3, 2),                        -- ID: 28
('Prácticas Profesionalizantes III', 3, 2);          -- ID: 29

INSERT INTO Horarios (id_materia, id_profesor, dia_semana, hora_inicio, hora_fin) VALUES
(25, 19, 'Lunes', '18:00', '22:15'),     -- Inst Ind / Fuentes
(26, 14, 'Martes', '18:40', '20:55'),    -- Ingles / Schvartz
(27, 21, 'Miércoles', '18:00', '22:15'), -- Inst Renov / Fuentes, Pons, Schvartz
(28, 16, 'Jueves', '18:40', '22:15'),    -- Gestion / Pons
(29, 19, 'Viernes', '18:00', '22:15');   -- Pract III / Fuentes

-- ==========================================
-- 6. INSERTAR ANUNCIO DE EJEMPLO
-- ==========================================

INSERT INTO anuncios (titulo, contenido, estado, destacado) VALUES
('Bienvenidos al Panel de Administración', 'Este es un anuncio de prueba. Puedes editarlo o eliminarlo desde el panel.', 'publicado', 1);

-- ==========================================
-- FIN DEL SCRIPT
-- ==========================================
SELECT 'Tablas creadas y datos insertados exitosamente' AS mensaje;
