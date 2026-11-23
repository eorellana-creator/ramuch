<?php
require_once __DIR__ . '/permisos.php';

if (!puedeAdministrarInventario($mysql)) {
    http_response_code(403);
    echo "<div class='alert alert-danger'>No tiene permisos para acceder al inventario de equipos.</div>";
    return;
}

include("controller.php");
?>
