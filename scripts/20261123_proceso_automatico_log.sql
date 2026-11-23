-- Registro de ejecuciones de crons y procesos automáticos (Dashboard de administradores).
-- admin/includes/procesos_automaticos.php crea la tabla automáticamente si no existe;
-- este script permite crearla de antemano al publicar en producción.
CREATE TABLE IF NOT EXISTS proceso_automatico_log (
    id_log INT NOT NULL AUTO_INCREMENT,
    proceso VARCHAR(60) NOT NULL,
    titulo VARCHAR(200) NOT NULL,
    descripcion TEXT NULL,
    resultado VARCHAR(10) NOT NULL,
    fecha_inicio DATETIME NOT NULL,
    fecha_fin DATETIME NOT NULL,
    duracion_segundos INT NOT NULL DEFAULT 0,
    correo_enviado TINYINT(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (id_log),
    KEY fecha_fin (fecha_fin),
    KEY proceso (proceso)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Reversa:
-- DROP TABLE IF EXISTS proceso_automatico_log;
