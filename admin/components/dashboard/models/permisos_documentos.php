<?php

function puedeAdministrarDocumentos($mysql)
{
    $idUsuario = isset($_SESSION['usuario_id']) ? (int) $_SESSION['usuario_id'] : 0;
    if ($idUsuario === 1) {
        return true;
    }

    $idRol = isset($_SESSION['usuario_rol']) ? (int) $_SESSION['usuario_rol'] : 0;
    if ($idRol <= 0) {
        return false;
    }

    $consulta = $mysql->query("SELECT nombre FROM rol WHERE id_rol='$idRol' LIMIT 1;");
    $rol = $consulta ? $mysql->f_obj($consulta) : null;

    return $rol && strtolower(trim($rol->nombre)) === 'administrador de socios';
}

