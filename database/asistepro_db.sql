-- ============================================================
-- AsistePro - Script de Base de Datos
-- Sistema de Control de Asistencia Estudiantil
-- ISTAE - Instituto Superior Tecnológico Alberto Enríquez
--
-- Instrucciones:
--   1. Abrir phpMyAdmin en http://localhost/phpmyadmin
--   2. Ir a la pestaña "Importar"
--   3. Seleccionar este archivo y ejecutar
--
-- Contraseña de demostración para TODOS los usuarios: Demo1234!
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "-05:00";
SET NAMES utf8mb4;

-- Crear base de datos
DROP DATABASE IF EXISTS `asistepro_db`;
CREATE DATABASE `asistepro_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `asistepro_db`;

-- ============================================================
-- TABLA: roles
-- ============================================================
CREATE TABLE `roles` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(50) NOT NULL UNIQUE,
    `descripcion` VARCHAR(255) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: usuarios
-- ============================================================
CREATE TABLE `usuarios` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(100) NOT NULL,
    `apellido` VARCHAR(100) NOT NULL,
    `email` VARCHAR(191) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `rol_id` INT UNSIGNED NOT NULL,
    `activo` TINYINT(1) NOT NULL DEFAULT 1,
    `ultimo_acceso` DATETIME DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_usuarios_rol` (`rol_id`),
    INDEX `idx_usuarios_email` (`email`),
    INDEX `idx_usuarios_activo` (`activo`),
    CONSTRAINT `fk_usuarios_rol` FOREIGN KEY (`rol_id`) REFERENCES `roles`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: cursos
-- ============================================================
CREATE TABLE `cursos` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(150) NOT NULL,
    `codigo` VARCHAR(20) NOT NULL UNIQUE,
    `descripcion` TEXT DEFAULT NULL,
    `docente_id` INT UNSIGNED DEFAULT NULL,
    `periodo` VARCHAR(20) DEFAULT '2026-2027',
    `activo` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_cursos_docente` (`docente_id`),
    INDEX `idx_cursos_activo` (`activo`),
    CONSTRAINT `fk_cursos_docente` FOREIGN KEY (`docente_id`) REFERENCES `usuarios`(`id`) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: estudiantes_cursos (matrículas)
-- ============================================================
CREATE TABLE `estudiantes_cursos` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `estudiante_id` INT UNSIGNED NOT NULL,
    `curso_id` INT UNSIGNED NOT NULL,
    `fecha_matricula` DATE NOT NULL DEFAULT (CURRENT_DATE),
    `activo` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_estudiante_curso` (`estudiante_id`, `curso_id`),
    INDEX `idx_ec_curso` (`curso_id`),
    CONSTRAINT `fk_ec_estudiante` FOREIGN KEY (`estudiante_id`) REFERENCES `usuarios`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `fk_ec_curso` FOREIGN KEY (`curso_id`) REFERENCES `cursos`(`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: asistencias
-- ============================================================
CREATE TABLE `asistencias` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `estudiante_id` INT UNSIGNED NOT NULL,
    `curso_id` INT UNSIGNED NOT NULL,
    `fecha` DATE NOT NULL,
    `estado` ENUM('presente','ausente','atrasado','justificado') NOT NULL DEFAULT 'presente',
    `observacion` TEXT DEFAULT NULL,
    `registrado_por` INT UNSIGNED DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_asistencia_unica` (`estudiante_id`, `curso_id`, `fecha`),
    INDEX `idx_asistencia_fecha` (`fecha`),
    INDEX `idx_asistencia_estado` (`estado`),
    INDEX `idx_asistencia_curso` (`curso_id`),
    INDEX `idx_asistencia_registrado` (`registrado_por`),
    CONSTRAINT `fk_asistencia_estudiante` FOREIGN KEY (`estudiante_id`) REFERENCES `usuarios`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `fk_asistencia_curso` FOREIGN KEY (`curso_id`) REFERENCES `cursos`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `fk_asistencia_registrador` FOREIGN KEY (`registrado_por`) REFERENCES `usuarios`(`id`) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: alertas
-- ============================================================
CREATE TABLE `alertas` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `estudiante_id` INT UNSIGNED NOT NULL,
    `curso_id` INT UNSIGNED DEFAULT NULL,
    `tipo` VARCHAR(50) NOT NULL DEFAULT 'inasistencia',
    `mensaje` TEXT NOT NULL,
    `leida` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_alertas_estudiante` (`estudiante_id`),
    INDEX `idx_alertas_leida` (`leida`),
    CONSTRAINT `fk_alertas_estudiante` FOREIGN KEY (`estudiante_id`) REFERENCES `usuarios`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `fk_alertas_curso` FOREIGN KEY (`curso_id`) REFERENCES `cursos`(`id`) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: configuracion
-- ============================================================
CREATE TABLE `configuracion` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `clave` VARCHAR(100) NOT NULL UNIQUE,
    `valor` TEXT DEFAULT NULL,
    `descripcion` VARCHAR(255) DEFAULT NULL,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: log_acciones
-- ============================================================
CREATE TABLE `log_acciones` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `usuario_id` INT UNSIGNED DEFAULT NULL,
    `accion` VARCHAR(255) NOT NULL,
    `detalle` TEXT DEFAULT NULL,
    `ip` VARCHAR(45) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_log_usuario` (`usuario_id`),
    INDEX `idx_log_fecha` (`created_at`),
    CONSTRAINT `fk_log_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- DATOS DE DEMOSTRACIÓN
-- ============================================================

-- Roles
INSERT INTO `roles` (`id`, `nombre`, `descripcion`) VALUES
(1, 'administrador', 'Acceso completo al sistema'),
(2, 'docente', 'Gestión de cursos y asistencia'),
(3, 'estudiante', 'Consulta de asistencia propia');

-- Contraseña para TODOS los usuarios de demo: Demo1234!
SET @demo_pass = '$2y$10$TaXVL/RYz/di4qFDKlawc.5qXPw5ylP0kIT6v0iWoAAf44xpVP9OO';

-- Usuario administrador
INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `email`, `password`, `rol_id`) VALUES
(1, 'Administrador', 'Sistema', 'admin@asistepro.local', @demo_pass, 1);

-- Docentes
INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `email`, `password`, `rol_id`) VALUES
(2, 'Carlos', 'Mendoza', 'carlos.mendoza@asistepro.local', @demo_pass, 2),
(3, 'María', 'López', 'maria.lopez@asistepro.local', @demo_pass, 2);

-- Estudiantes (IDs 4-13)
INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `email`, `password`, `rol_id`) VALUES
(4,  'Juan',      'Pérez',    'juan.perez@asistepro.local',      @demo_pass, 3),
(5,  'Ana',       'Torres',   'ana.torres@asistepro.local',      @demo_pass, 3),
(6,  'Luis',      'García',   'luis.garcia@asistepro.local',     @demo_pass, 3),
(7,  'Sofía',     'Ramírez',  'sofia.ramirez@asistepro.local',   @demo_pass, 3),
(8,  'Pedro',     'Morales',  'pedro.morales@asistepro.local',   @demo_pass, 3),
(9,  'Carolina',  'Vega',     'carolina.vega@asistepro.local',   @demo_pass, 3),
(10, 'Andrés',    'Salazar',  'andres.salazar@asistepro.local',  @demo_pass, 3),
(11, 'Valentina', 'Cruz',     'valentina.cruz@asistepro.local',  @demo_pass, 3),
(12, 'Diego',     'Herrera',  'diego.herrera@asistepro.local',   @demo_pass, 3),
(13, 'Camila',    'Rojas',    'camila.rojas@asistepro.local',    @demo_pass, 3);

-- Cursos
INSERT INTO `cursos` (`id`, `nombre`, `codigo`, `descripcion`, `docente_id`, `periodo`) VALUES
(1, 'Desarrollo de Software', 'DS-401', 'Fundamentos de programación y desarrollo de aplicaciones web', 2, '2026-2027'),
(2, 'Base de Datos',          'BD-401', 'Diseño y administración de bases de datos relacionales',       2, '2026-2027'),
(3, 'Redes y Comunicaciones', 'RC-401', 'Infraestructura de redes, protocolos y comunicaciones',        3, '2026-2027');

-- Matrículas: 10 estudiantes en Desarrollo de Software
INSERT INTO `estudiantes_cursos` (`estudiante_id`, `curso_id`, `fecha_matricula`) VALUES
(4,  1, '2026-10-01'), (5,  1, '2026-10-01'), (6,  1, '2026-10-01'),
(7,  1, '2026-10-01'), (8,  1, '2026-10-01'), (9,  1, '2026-10-01'),
(10, 1, '2026-10-01'), (11, 1, '2026-10-01'), (12, 1, '2026-10-01'),
(13, 1, '2026-10-01');

-- Matrículas: 7 estudiantes en Base de Datos
INSERT INTO `estudiantes_cursos` (`estudiante_id`, `curso_id`, `fecha_matricula`) VALUES
(4,  2, '2026-10-01'), (5,  2, '2026-10-01'), (6,  2, '2026-10-01'),
(7,  2, '2026-10-01'), (8,  2, '2026-10-01'), (9,  2, '2026-10-01'),
(10, 2, '2026-10-01');

-- Matrículas: 5 estudiantes en Redes y Comunicaciones
INSERT INTO `estudiantes_cursos` (`estudiante_id`, `curso_id`, `fecha_matricula`) VALUES
(4,  3, '2026-10-01'), (5,  3, '2026-10-01'), (6,  3, '2026-10-01'),
(7,  3, '2026-10-01'), (8,  3, '2026-10-01');

-- ============================================================
-- ASISTENCIA DE DEMOSTRACIÓN
-- ============================================================
DELIMITER //
CREATE PROCEDURE generar_asistencia_demo()
BEGIN
    DECLARE v_fecha DATE;
    DECLARE v_dias INT DEFAULT 0;
    DECLARE v_estudiante_id INT;
    DECLARE v_rand FLOAT;
    DECLARE v_estado VARCHAR(20);

    SET v_fecha = DATE_SUB(CURDATE(), INTERVAL 28 DAY);

    WHILE v_dias < 20 DO
        IF DAYOFWEEK(v_fecha) NOT IN (1, 7) THEN
            SET v_dias = v_dias + 1;

            SET v_estudiante_id = 4;
            WHILE v_estudiante_id <= 13 DO
                SET v_rand = RAND();

                IF v_estudiante_id IN (12, 13) THEN
                    IF v_rand < 0.35 THEN
                        SET v_estado = 'ausente';
                    ELSEIF v_rand < 0.55 THEN
                        SET v_estado = 'atrasado';
                    ELSEIF v_rand < 0.60 THEN
                        SET v_estado = 'justificado';
                    ELSE
                        SET v_estado = 'presente';
                    END IF;
                ELSEIF v_estudiante_id = 8 THEN
                    IF v_rand < 0.10 THEN
                        SET v_estado = 'ausente';
                    ELSEIF v_rand < 0.40 THEN
                        SET v_estado = 'atrasado';
                    ELSEIF v_rand < 0.45 THEN
                        SET v_estado = 'justificado';
                    ELSE
                        SET v_estado = 'presente';
                    END IF;
                ELSE
                    IF v_rand < 0.08 THEN
                        SET v_estado = 'ausente';
                    ELSEIF v_rand < 0.20 THEN
                        SET v_estado = 'atrasado';
                    ELSEIF v_rand < 0.25 THEN
                        SET v_estado = 'justificado';
                    ELSE
                        SET v_estado = 'presente';
                    END IF;
                END IF;

                INSERT INTO `asistencias` (`estudiante_id`, `curso_id`, `fecha`, `estado`, `registrado_por`)
                VALUES (v_estudiante_id, 1, v_fecha, v_estado, 1);

                SET v_estudiante_id = v_estudiante_id + 1;
            END WHILE;
        END IF;

        SET v_fecha = DATE_ADD(v_fecha, INTERVAL 1 DAY);
    END WHILE;
END //
DELIMITER ;

CALL generar_asistencia_demo();
DROP PROCEDURE IF EXISTS generar_asistencia_demo;

-- ============================================================
-- ALERTAS DE DEMOSTRACIÓN
-- ============================================================
INSERT INTO `alertas` (`estudiante_id`, `curso_id`, `tipo`, `mensaje`, `leida`) VALUES
(12, 1, 'inasistencia', 'Diego Herrera ha superado el 20% de inasistencias en Desarrollo de Software. Se recomienda seguimiento.', 0),
(13, 1, 'inasistencia', 'Camila Rojas ha superado el 20% de inasistencias en Desarrollo de Software. Se recomienda seguimiento.', 0),
(8,  1, 'atrasos',      'Pedro Morales tiene un alto número de atrasos en Desarrollo de Software.', 0),
(12, 1, 'inasistencia', 'Diego Herrera acumula 3 inasistencias consecutivas en Desarrollo de Software.', 1),
(13, 1, 'inasistencia', 'Camila Rojas acumula 2 inasistencias consecutivas en Desarrollo de Software.', 1);

-- ============================================================
-- CONFIGURACIÓN INSTITUCIONAL
-- ============================================================
INSERT INTO `configuracion` (`clave`, `valor`, `descripcion`) VALUES
('nombre_institucion', 'Instituto Superior Tecnológico Alberto Enríquez', 'Nombre de la institución'),
('siglas_institucion',  'ISTAE', 'Siglas de la institución'),
('porcentaje_alerta',   '20', 'Porcentaje de inasistencia para generar alerta'),
('periodo_actual',      '2026-2027', 'Periodo académico vigente'),
('dias_consecutivos_alerta', '3', 'Días consecutivos de ausencia para alerta');

-- ============================================================
-- LOG INICIAL
-- ============================================================
INSERT INTO `log_acciones` (`usuario_id`, `accion`, `detalle`, `ip`) VALUES
(1, 'Instalación del sistema', 'Base de datos creada con datos de demostración', '127.0.0.1');

COMMIT;
