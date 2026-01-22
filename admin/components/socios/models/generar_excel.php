<?php
session_start();
include("../../../includes/sql_inyection.php");
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

$config = new Config;
$mysql = new mysql;
$mysql->connect();

// Obtener los parámetros del formulario
$fechaInicio = $_GET['fechaInicio'];
$fechaFin = $_GET['fechaFin'];
$tipoListado = $_GET['tipoListado'];
$tipoInscripcion = explode(',', $_GET['tipoInscripcion']);

// Validar las fechas
if (empty($fechaInicio) || empty($fechaFin)) {
    die("Error: Debe proporcionar un rango de fechas válido.");
}

// Variable TI para almacenar las letras correspondientes a los tipos de inscripción
$TI = '';

// Si "todos" está seleccionado, incluir todos los tipos de inscripción
if (in_array('todos', $tipoInscripcion)) {
    $tipoInscripcion = ['1', '2', '3', '6', '7', '8'];
    $TI = 'T'; // Agregar solo una 'T' si se selecciona "Todos"
} else {
    // Construir la variable TI según los tipos de inscripción seleccionados
    foreach ($tipoInscripcion as $tipo) {
        switch ($tipo) {
            case '1':
                $TI .= 'P'; // Profesional
                break;
            case '2':
                $TI .= 'H'; // Honorario
                break;
            case '3':
                $TI .= 'E'; // Estudiante
                break;
            case '6':
                $TI .= 'C'; // Congelado
                break;
            case '7':
                $TI .= 'D'; // Desvinculado
                break;
            case '8':
                $TI .= 'L'; // Eliminado (usamos 'L' para evitar conflicto con 'E' de estudiante)
                break;
        }
    }
}

// Construir la consulta SQL dinámica
$sqlBase = "SELECT u.id_usuario, u.nombre_usuario, u.email, p.tipo_inscripcion, p.fono, p.rut, u.referencia, u.fecha_registro
            FROM usuario AS u 
            LEFT JOIN perfil AS p ON u.id_usuario = p.id_usuario 
            WHERE u.web_matricula_pagada = 'Si' AND 
            p.tipo_inscripcion IN ('" . implode("','", $tipoInscripcion) . "')";

$tipo_listado = "";
// Filtros adicionales según el tipo de listado
switch ($tipoListado) {
    case 'deudores':
        $sqlBase .= " AND EXISTS (
            SELECT 1 
            FROM deudas d 
            WHERE d.id_usuario_deuda = u.id_usuario 
              AND d.fecha BETWEEN '$fechaInicio' AND '$fechaFin' 
              AND d.estado = 'activa'
        )";
        $tipo_listado = "deudores";
        break;

    case 'alDia':
        $sqlBase .= " AND NOT EXISTS (
            SELECT 1 
            FROM deudas d 
            WHERE d.id_usuario_deuda = u.id_usuario 
              AND d.fecha BETWEEN '$fechaInicio' AND '$fechaFin' 
              AND d.estado = 'activa'
        )";
        $tipo_listado = "alDia";
        break;

    case 'todos':
        // No se aplican filtros adicionales
        break;

    default:
        die("Error: Tipo de listado no válido.");
}

// Ejecutar la consulta
$sqlC = $mysql->query($sqlBase);

error_log("sql: " . $sqlBase);

// Procesar los resultados
$lista_excel = "";
while ($resultC = $mysql->f_obj($sqlC)) {
    // Traducir el tipo de inscripción
    $tipoInscripcionTexto = '';
    switch ($resultC->tipo_inscripcion) {
        case '1':
            $tipoInscripcionTexto = 'Profesional';
            break;
        case '2':
            $tipoInscripcionTexto = 'Honorario';
            break;
        case '3':
            $tipoInscripcionTexto = 'Estudiante';
            break;
        case '6':
            $tipoInscripcionTexto = 'Congelado';
            break;
        case '7':
            $tipoInscripcionTexto = 'Desvinculado';
            break;
        case '8':
            $tipoInscripcionTexto = 'Eliminado';
            break;
        default:
            $tipoInscripcionTexto = 'Desconocido';
    }

    //agregar campo de deuda
    $hoy = date("Y-m-d");
    $sqlD 	= $mysql->query("SELECT SUM(monto) as deuda FROM deudas WHERE id_usuario_deuda='$resultC->id_usuario' AND fecha<'$hoy' AND estado='activa' ;");
    $resultD = $mysql->f_obj($sqlD);
    $deuda = 0;
    if($resultD->deuda>0)
    $deuda = number_format($resultD->deuda, 0, '', '.');

    //agregar campo de cantidad de meses con deuda
    $sqlE 	= $mysql->query("SELECT count(monto) as cantidad FROM deudas WHERE id_usuario_deuda='$resultC->id_usuario' AND fecha<'$hoy' AND estado='activa' ;");
    $resultE = $mysql->f_obj($sqlE);
    $cantidad = 0;
    if($resultE->cantidad>0)
    $cantidad = number_format($resultE->cantidad, 0, '', '.');  

    // Agregar fila al archivo Excel
    $lista_excel .= "<tr>
                        <td>{$resultC->id_usuario}</td>
                        <td>{$resultC->nombre_usuario}</td>
                        <td>{$resultC->rut}</td>
                        <td>{$resultC->fono}</td>
                        <td>{$resultC->email}</td>
                        <td>$tipoInscripcionTexto</td>
                        <td>{$resultC->referencia}</td>
                        <td>{$resultC->fecha_registro}</td>
                        <td>$deuda</td>
                        <td>$cantidad</td>
                      </tr>";
   
}//while

// Encabezados del archivo Excel
$encabezados = "<tr style='background-color:#313131; color:#ffffff;padding:4px;'>
                    <td>ID</td>
                    <td>Nombre</td>
                    <td>Rut</td>
                    <td>Teléfono</td>
                    <td>Email</td>
                    <td>Tipo de Inscripción</td>
                    <td>Referencia</td>
                    <td>Fecha de Registro</td>
                    <td>Deuda</td>
                    <td>Cantidad de cuotas</td>
                </tr>";

// Contenido completo del archivo Excel
$id_usuario_sesion = @$_SESSION["usuario_id"];
$archivo_nombre = "../excel/lista_socios-$tipo_listado-$TI-$id_usuario_sesion.xls";
$contenido_excel = "<html lang='es'>
                        <head><meta charset='UTF-8'></head>
                        <body>
                            <table>
                                $encabezados
                                $lista_excel
                            </table>
                        </body>
                     </html>";

// Guardar el archivo Excel
$fp = fopen($archivo_nombre, 'w+');
fwrite($fp, $contenido_excel);
fclose($fp);

// Respuesta al usuario
?>
<html lang="es">
<head></head>
<body>
    <?php if ($id_usuario_sesion != "") { ?>
        <div style="width:100%; padding:30px; text-align:center; font-size:16px; font-family: Arial;">
            Descarga el listado <br>
            <a href="<?php echo $archivo_nombre; ?>">Descárgalo haciendo click aquí</a>
        </div>
    <?php } ?>
</body>
</html>