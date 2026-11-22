-- Renombra los tipos de inscripción visibles (solicitudes #5 y #11 de la Intranet).
-- Solo cambia la descripción: los IDs (perfil.tipo_inscripcion) no cambian,
-- por lo que deudas, planes, cuotas y filtros siguen funcionando igual.
-- También corrige el error de tipeo "Honoranios".
--
-- Reversión:
--   UPDATE plan_matricula SET nombre = 'Profesional' WHERE id_plan_matricula = 1;
--   UPDATE plan_matricula SET nombre = 'Estudiante'  WHERE id_plan_matricula = 3;
--   UPDATE plan_matricula SET nombre = 'Honoranios'  WHERE id_plan_matricula = 2;

UPDATE plan_matricula SET nombre = 'Socio General'       WHERE id_plan_matricula = 1;
UPDATE plan_matricula SET nombre = 'Estudiante Pregrado' WHERE id_plan_matricula = 3;
UPDATE plan_matricula SET nombre = 'Honorario'           WHERE id_plan_matricula = 2;
