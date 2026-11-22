<?php
/**
 * Nombres de los tipos de inscripción.
 *
 * Los socios guardan solamente el ID (perfil.tipo_inscripcion), que apunta a
 * plan_matricula.id_plan_matricula. El nombre visible se lee de
 * plan_matricula.nombre para que pueda cambiarse desde la base sin tocar el
 * código. Los IDs nunca cambian: 1 Socio General, 2 Honorario, 3 Estudiante
 * Pregrado, 6 Congelado, 7 Desvinculado, 8 Eliminado.
 *
 * Los nombres de respaldo solo se usan si la tabla no puede leerse.
 */

if (!function_exists('ramuchNombresTipoInscripcion')) {
    function ramuchNombresTipoInscripcion($mysql)
    {
        static $cache = null;
        if ($cache !== null) {
            return $cache;
        }

        $nombres = array(
            1 => 'Socio General',
            2 => 'Honorario',
            3 => 'Estudiante Pregrado',
            6 => 'Congelado',
            7 => 'Desvinculado',
            8 => 'Eliminado',
        );

        // Sin conexión se responden los nombres de respaldo, sin guardarlos en
        // caché, para que una llamada posterior con conexión lea la tabla.
        if (!is_object($mysql)) {
            return $nombres;
        }

        $sql = $mysql->query('SELECT id_plan_matricula, nombre FROM plan_matricula;');
        if (!$sql) {
            return $nombres;
        }
        while ($fila = $mysql->f_obj($sql)) {
            $nombre = trim((string)$fila->nombre);
            if ($nombre !== '') {
                $nombres[(int)$fila->id_plan_matricula] = $nombre;
            }
        }

        $cache = $nombres;
        return $cache;
    }

    function ramuchNombreTipoInscripcion($mysql, $idTipo, $porDefecto = '')
    {
        $nombres = ramuchNombresTipoInscripcion($mysql);
        return isset($nombres[(int)$idTipo]) ? $nombres[(int)$idTipo] : $porDefecto;
    }
}
