<?php

function puedeAdministrarInventario($mysql)
{
    if (($_SESSION['usuario_valido_bastro_ruta'] ?? '') !== 'true') {
        return false;
    }

    $idRol = isset($_SESSION['usuario_rol']) ? (int) $_SESSION['usuario_rol'] : 0;
    if ($idRol <= 0) {
        return false;
    }

    $sql = $mysql->query(
        "SELECT COUNT(*) AS cantidad
         FROM menu_rol AS mr
         INNER JOIN menu AS m ON m.id_menu = mr.id_menu
         WHERE mr.id_rol = '$idRol'
           AND mr.activo = '1'
           AND m.activo = '1'
           AND m.url LIKE '%component=equipo%'
           AND m.url LIKE '%view=equipo_list%'"
    );
    $permiso = $mysql->f_obj($sql);

    return (int) ($permiso->cantidad ?? 0) > 0;
}

