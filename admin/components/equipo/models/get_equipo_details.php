<?php
session_start();
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

$token = $_GET['token'];
$mysql = new mysql;
$mysql->connect();

$sql = $mysql->query("SELECT * FROM equipo WHERE token = '$token' LIMIT 1");
$equipo = $mysql->f_obj($sql);

if($equipo) {
    // Obtener historial de préstamos
    $sql_historial = $mysql->query("SELECT * FROM equipo_prestamo WHERE id_equipo = '$equipo->id_equipo' ORDER BY fecha_prestamo DESC");
    
    $historial = "";
    while($prestamo = $mysql->f_obj($sql_historial)) {
        $fecha_prestamo = $prestamo->fecha_prestamo != '0000-00-00' ? fecha_mysql_a_normal($prestamo->fecha_prestamo) : '';
        $fecha_devolucion = $prestamo->fecha_devolucion_efectiva != '0000-00-00' ? fecha_mysql_a_normal($prestamo->fecha_devolucion_efectiva) : '';
        
        // Obtener nombres de usuarios
        $sql_usuario = $mysql->query("SELECT nombre_usuario FROM usuario WHERE id_usuario = '$prestamo->id_usuario_prestamo'");
        $usuario = $mysql->f_obj($sql_usuario);
        
        // Nombre del usuario responsable (si existe ID)
        $nombre_responsable = ''; // Valor por defecto
        if (!empty($prestamo->id_usuario_responsable)) {
            $sql6 = $mysql->query("SELECT nombre_usuario FROM usuario WHERE id_usuario='$prestamo->id_usuario_responsable'");
            $result6 = $mysql->f_obj($sql6);
            $nombre_responsable = ($result6 && isset($result6->nombre_usuario)) ? $result6->nombre_usuario : '';
        }

        // Nombre del usuario que recibió (si existe ID)
        $nombre_recepciono = ''; // Valor por defecto
        if (!empty($prestamo->id_usuario_recepciono)) {
            $sql7 = $mysql->query("SELECT nombre_usuario FROM usuario WHERE id_usuario='$prestamo->id_usuario_recepciono'");
            $result7 = $mysql->f_obj($sql7);
            $nombre_recepciono = ($result7 && isset($result7->nombre_usuario)) ? $result7->nombre_usuario : '';
        }

        $historial .= "<tr>
            <td>$fecha_prestamo</td>
            <td>$fecha_devolucion</td>
            <td>" . ($usuario ? $usuario->nombre_usuario : '') . "</td>
            <td>Responsable:<br>$nombre_responsable<br>Recepcionó:<br>$nombre_recepciono</td>
            <td>$prestamo->estado_devolucion</td>
            <td>$prestamo->comentario</td>
        </tr>";
    }

    echo "<div class='container-fluid'>
            <div class='row'>
                <div class='col-md-4'>
                    <img src='" . ($equipo->imagen ? "images/equipo/$equipo->imagen" : "images/equipo_sin_imagen.jpg") . "' 
                         class='img-fluid' alt='Imagen del equipo'>
                </div>
                <div class='col-md-8'>
                    <h4>$equipo->nombre</h4>
                    <p><strong>ID Único:</strong> $equipo->id_unico</p>
                    <p><strong>Estado:</strong> $equipo->estado</p>
                    <p><strong>Marca:</strong> $equipo->marca</p>
                    <p><strong>Talla:</strong> $equipo->talla</p>
                    <p><strong>Descripción:</strong> $equipo->descripcion</p>
                    <p><strong>Observación:</strong> $equipo->observacion</p>
                </div>
            </div>
            <div class='row mt-4'>
                <div class='col-12'>
                    <h5>Historial de Préstamos</h5>
                    <div class='table-responsive'>
                        <table class='table table-striped'>
                            <thead>
                                <tr>
                                    <th>Fecha Préstamo</th>
                                    <th>Fecha Devolución</th>
                                    <th>Usuario</th>
                                    <th>Responsables</th>
                                    <th>Estado</th>
                                    <th>Observaciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                $historial
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
          </div>";
} else {
    echo "<p class='text-center'>No se encontró información del equipo.</p>";
}
?>