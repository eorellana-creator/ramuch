-- Sincroniza usuarios tipo 7 y 8 y desactiva exclusivamente sus deudas activas.
-- No crea tablas, no elimina registros y no modifica cuenta_maestra.

START TRANSACTION;

-- Tipo 7 = Deshabilitado; tipo 8 = Eliminado.
UPDATE usuario AS u
INNER JOIN perfil AS p
    ON p.id_usuario = u.id_usuario
SET u.estado = CASE p.tipo_inscripcion
    WHEN 7 THEN 'Deshabilitado'
    WHEN 8 THEN 'Eliminado'
END
WHERE p.tipo_inscripcion IN (7, 8)
  AND LOWER(TRIM(COALESCE(u.estado, ''))) <>
      CASE p.tipo_inscripcion
          WHEN 7 THEN 'deshabilitado'
          WHEN 8 THEN 'eliminado'
      END;

SELECT ROW_COUNT() AS usuarios_actualizados;

-- Solo deudas actualmente activas de usuarios tipo 7 u 8.
UPDATE deudas AS d
INNER JOIN perfil AS p
    ON p.id_usuario = d.id_usuario_deuda
SET
    d.estado = 'desactivada',
    d.fecha_modificacion = CURRENT_DATE()
WHERE p.tipo_inscripcion IN (7, 8)
  AND LOWER(TRIM(d.estado)) = 'activa';

SELECT ROW_COUNT() AS deudas_desactivadas;

COMMIT;

-- Verificación: ambas consultas deben devolver cero filas.
SELECT u.id_usuario, u.nombre_usuario, u.estado, p.tipo_inscripcion
FROM usuario AS u
INNER JOIN perfil AS p
    ON p.id_usuario = u.id_usuario
WHERE p.tipo_inscripcion IN (7, 8)
  AND LOWER(TRIM(u.estado)) <>
      CASE p.tipo_inscripcion
          WHEN 7 THEN 'deshabilitado'
          WHEN 8 THEN 'eliminado'
      END;

SELECT u.id_usuario, u.nombre_usuario, u.estado,
       d.id_deuda, d.estado AS estado_deuda
FROM usuario AS u
INNER JOIN perfil AS p
    ON p.id_usuario = u.id_usuario
INNER JOIN deudas AS d
    ON d.id_usuario_deuda = u.id_usuario
WHERE p.tipo_inscripcion IN (7, 8)
  AND LOWER(TRIM(d.estado)) = 'activa';

