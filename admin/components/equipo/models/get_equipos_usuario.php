<?php
session_start();
include("../../../includes/sql_inyection_salto_textarea.php");
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

$mysql = new mysql;
$mysql->connect();

$idUsuario = $_GET['id_usuario'] ?? 0;

// Consulta corregida
$sql = "
    SELECT 
        ep.id_equipo_prestamo,  
        e.id_equipo,            
        e.nombre
    FROM equipo_prestamo ep
    INNER JOIN equipo e ON ep.id_equipo = e.id_equipo
    WHERE 
        ep.estado = 'prestado' AND
        e.prestado_a_id_usuario = '$idUsuario'
";

$result = $mysql->query($sql);
$equipos = [];

while ($row = $result->fetch_assoc()) {
    $equipos[] = $row;
}

echo json_encode($equipos);
?>