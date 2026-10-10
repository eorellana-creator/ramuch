-- Nuevos antecedentes del formulario de inscripción RAMUCH.
--
-- Consideraciones:
--   * Los nombres y apellidos se seguirán guardando concatenados en los
--     campos existentes perfil.nombre y usuario.nombre_usuario.
--   * La fecha de nacimiento, RUT, email, teléfono, tipo de inscripción y
--     certificado de alumno regular ya tienen campos en el modelo actual.
--   * Los registros históricos quedan con NULL en los nuevos campos para no
--     atribuirles respuestas ni consentimientos que nunca proporcionaron.
--   * El correo de cumpleaños utilizará perfil.fecha_nacimiento y no requiere
--     una tabla ni un campo adicional.

ALTER TABLE perfil
    ADD COLUMN sexo_genero VARCHAR(20) NULL COMMENT 'Femenino, Masculino u Otro',
    ADD COLUMN otro_club_montanismo TINYINT(1) NULL COMMENT '1=Sí, 0=No, NULL=Sin información',
    ADD COLUMN nombre_otro_club VARCHAR(50) NULL COMMENT 'Nombre del club de montañismo anterior',
    ADD COLUMN motivaciones_ingreso TEXT NULL COMMENT 'Hasta tres códigos de motivación codificados como JSON',
    ADD COLUMN motivacion_otro VARCHAR(255) NULL COMMENT 'Detalle cuando se selecciona la motivación Otro',
    ADD COLUMN consentimiento_convenios TINYINT(1) NULL COMMENT '1=Autorización aceptada, 0=Revocada, NULL=Sin registro',
    ADD COLUMN consentimiento_fecha DATETIME NULL COMMENT 'Fecha y hora de aceptación del consentimiento',
    ADD COLUMN consentimiento_version VARCHAR(30) NULL COMMENT 'Versión del texto legal aceptado',
    ADD COLUMN consentimiento_revocado_fecha DATETIME NULL COMMENT 'Fecha y hora de una eventual revocación';

-- Verificación posterior a la migración.
SELECT
    COLUMN_NAME,
    COLUMN_TYPE,
    IS_NULLABLE,
    COLUMN_COMMENT
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'perfil'
  AND COLUMN_NAME IN (
      'sexo_genero',
      'otro_club_montanismo',
      'nombre_otro_club',
      'motivaciones_ingreso',
      'motivacion_otro',
      'consentimiento_convenios',
      'consentimiento_fecha',
      'consentimiento_version',
      'consentimiento_revocado_fecha'
  )
ORDER BY ORDINAL_POSITION;
