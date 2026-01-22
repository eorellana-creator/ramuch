<?php
session_start();
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

$mysql->connect();

$token = @$_GET["token"];
$isModal = isset($_GET['modal']) && $_GET['modal'] == '1';

$sql = $mysql->query("SELECT * FROM equipo WHERE token ='$token' AND token!='' ;");
$result = $mysql->f_obj($sql);
$id_equipo = @$result->id_equipo;

if(@$result->imagen!=""){
    $imagen = "<img src='images/equipo/$result->imagen' alt='' width='250'>";
} else {
    $imagen = "<img src='images/equipo_sin_imagen.jpg' alt='' width='90'>";
}

$estado = "<option value=''>Seleccionar</option>";
if(@$result->estado!="")
    $estado = "<option value='$result->estado'>$result->estado</option>";

$mensaje = @$_SESSION["equipo_actualizado"];
$_SESSION["equipo_actualizado"] = "";

//HISTORIAL
$div_historial = "";

$sql2 = $mysql->query("SELECT * FROM equipo_prestamo WHERE id_equipo='$id_equipo' ORDER BY fecha_prestamo DESC ;");
while($result2 = $mysql->f_obj($sql2)){
    $fecha_prestamo = "";
    $fecha_compromiso = "";
    $fecha_devolucion = "";

    $fecha_2 = strtotime($result2->fecha_devolucion_efectiva);
    $fecha_1 = strtotime($result2->fecha_debe_devolver);

    $color_fecha = "";
    if($fecha_2 > $fecha_1)
        $color_fecha = " style='color:#ff0000;' ";

    if($result2->fecha_prestamo>"0000-00-00")
        $fecha_prestamo = fecha_mysql_a_normal($result2->fecha_prestamo);

    if($result2->fecha_debe_devolver>"0000-00-00")
        $fecha_compromiso = fecha_mysql_a_normal($result2->fecha_debe_devolver);

    if($result2->fecha_devolucion_efectiva>"0000-00-00")
        $fecha_devolucion = fecha_mysql_a_normal($result2->fecha_devolucion_efectiva);

    $sql5 = $mysql->query("SELECT nombre_usuario FROM usuario WHERE id_usuario='$result2->id_usuario_prestamo'  ;");
    $result5 = $mysql->f_obj($sql5);
    $nombre_prestamo = @$result5->nombre_usuario;

    $sql6 = $mysql->query("SELECT nombre_usuario FROM usuario WHERE id_usuario='$result2->id_usuario_responsable'  ;");
    $result6 = $mysql->f_obj($sql6);
    $nombre_responsable = @$result6->nombre_usuario;

    $sql7 = $mysql->query("SELECT nombre_usuario FROM usuario WHERE id_usuario='$result2->id_usuario_recepciono'  ;");
    $result7 = $mysql->f_obj($sql7);
    $nombre_recepciono = @$result7->nombre_usuario;

    $div_historial .= "<tr>
        <td>$fecha_prestamo</td>
        <td>$fecha_compromiso</td>
        <td $color_fecha>$fecha_devolucion</td>
        <td>$nombre_prestamo</td>
        <td>$nombre_responsable</td>
        <td>$nombre_recepciono</td>
        <td>$result2->comentario</td>
        <td>$result2->estado_devolucion</td>
    </tr>";
}

if($div_historial != ""){
    $div_historial = "<div id='divtablah'> 
        <table class='blueTable'> 
            <thead>
                <tr>
                    <th>Fecha Préstamo</th>
                    <th>Fecha compromiso devolución</th>
                    <th>Fecha efectiva de devolución</th>
                    <th>Prestado a</th>
                    <th>Responsable del Préstamo</th>
                    <th>Recepcionó la devolución</th>
                    <th>Observación</th>
                    <th>Estado de la devolución</th>
                </tr>
            </thead>
            <tbody>
                $div_historial
            </tbody>
        </table>
    </div>";
} else {
    $div_historial = "<strong>Sin historial de préstamo.</strong>";
}

if ($isModal) {
    ?>
    <div class="row">
        <div class="col-md-4">
            <?php echo $imagen; ?>
        </div>
        <div class="col-md-8">
            <table class="table">
                <tr>
                    <th>Nombre:</th>
                    <td><?php echo @$result->nombre; ?></td>
                </tr>
                <tr>
                    <th>ID Único:</th>
                    <td><?php echo @$result->id_unico; ?></td>
                </tr>
                <tr>
                    <th>Estado:</th>
                    <td><?php echo @$result->estado; ?></td>
                </tr>
                <tr>
                    <th>Observación:</th>
                    <td><?php echo @$result->observacion; ?></td>
                </tr>
                <tr>
                    <th>Marca:</th>
                    <td><?php echo @$result->marca; ?></td>
                </tr>
                <tr>
                    <th>Talla:</th>
                    <td><?php echo @$result->talla; ?></td>
                </tr>
                <tr>
                    <th>Fecha Compra:</th>
                    <td><?php echo @$result->fecha_compra ? fecha_mysql_a_normal($result->fecha_compra) : ''; ?></td>
                </tr>
                <tr>
                    <th>Fecha Mantención:</th>
                    <td><?php echo @$result->fecha_mantencion ? fecha_mysql_a_normal($result->fecha_mantencion) : ''; ?></td>
                </tr>
                <tr>
                    <th>Descripción:</th>
                    <td><?php echo @$result->descripcion; ?></td>
                </tr>
            </table>
        </div>
    </div>
    <?php if ($div_historial != ""): ?>
    <div class="row mt-4">
        <div class="col-12">
            <h5>Historial del Equipo</h5>
            <?php echo $div_historial; ?>
        </div>
    </div>
    <?php endif;
    return;
}
?>

<?php echo $mensaje; ?>

<div class="card">
    <div class="card-header">
        <i class="fas fa-hiking"></i> Equipo Ramuch
    </div>
    <div class="card-body">
        <form name="formulario" id="formulario" method="post" action="javascript: enviar();" enctype="multipart/form-data">
            <input id="token" name="token" type="hidden" value="<?php echo $token; ?>">
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-lg-3 form-group"> 
                        <label class="col-form-label"><span class="obligatorio">*</span>Nombre del Equipo:</label>
                        <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Nombre del equipo" value="<?php echo @$result->nombre; ?>" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);" maxlength="255">
                    </div>

                    <div class="col-lg-2 form-group"> 
                        <label class="col-form-label">Identificador del Equipo:</label>
                        <input type="text" class="form-control" name="idunico" id="idunico" placeholder="ID único para el equipo" value="<?php echo @$result->id_unico; ?>" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);verificaid('<?php echo @$result->token; ?>',this.value);" maxlength="255">
                    </div>

                    <div class="col-lg-2 form-group"> 
                        <label class="col-form-label">Fecha Compra:</label>
                        <input class="form-control" id="fechaCompra" type="date" name="fechaCompra" placeholder="date" value="<?php echo @$result->fecha_compra; ?>">
                    </div>

                    <div class="col-lg-2 form-group"> 
                        <label class="col-form-label">Fecha Mantención:</label>
                        <input class="form-control" id="fechaMantencion" type="date" name="fechaMantencion" placeholder="date" value="<?php echo @$result->fecha_mantencion; ?>">
                    </div>

                    <div class="col-lg-2 form-group"> 
                        <label class="col-form-label"><span class="obligatorio">*</span>Estado del Equipo:</label>
                        <select id="estado" name="estado" class="form-control">
                            <?php echo @$estado; ?>
                            <option value="En buen estado">En buen estado</option>
                            <option value="Con detalles">Con detalles</option>
                            <option value="Extraviado">Extraviado</option>
                            <option value="Inutilizable">Inutilizable</option>
                            <option value="Dado de baja">Dado de baja</option>
                        </select>
                    </div>

                    <div class="col-lg-5 form-group"> 
                        <label class="col-form-label">Observación del estado del Equipo:</label>
                        <input type="text" class="form-control" name="observacion" id="observacion" placeholder="Observación del equipo" value="<?php echo @$result->observacion; ?>" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);" maxlength="255">
                    </div>

                    <div class="col-lg-2 form-group"> 
                        <label class="col-form-label">Marca del Equipo:</label>
                        <input type="text" class="form-control" name="marca" id="marca" placeholder="Marca del equipo" value="<?php echo @$result->marca; ?>" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);" maxlength="255">
                    </div>

                    <div class="col-lg-2 form-group"> 
                        <label class="col-form-label">Talla del Equipo:</label>
                        <input type="text" class="form-control" name="talla" id="talla" placeholder="Talla del equipo" value="<?php echo @$result->talla; ?>" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);" maxlength="255">
                    </div>

                    <div class="col-lg-8 form-group"> 
                        <label class="col-form-label">Descripción del Equipo:</label>
                        <textarea id="descripcion" name="descripcion" class="form-control" placeholder="Descripcion del equipo" onblur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);"><?php echo @$result->descripcion; ?></textarea>
                    </div>

                    <div class="col-lg-12 form-group"> 
                        <label class="col-form-label">Foto del Equipo (proporción 4:3 (alto x ancho). Ejemplo de medidas recomendadas: 120px x 90px || 1024px x 768px || 1280px x 720px):</label><br>
                        <input id="archivo" name="archivo" type="file" onChange="validaImagen(this);" class="btn btn-success btn-xs" />
                        <div><?php echo @$certificado; ?></div>
                    </div>

                    <div class="col-lg-4 form-group"> 
                        <?php echo @$imagen; ?>
                    </div>

                    <div class="col-lg-8 form-group"> 
                        Historial del Equipo<br>
                        <?php echo $div_historial; ?>
                    </div>

                    <div class="col-lg-12 text-center">
                        <br>
                        <a href="index.php?component=equipo&view=equipo_list">
                            <button type="button" class="btn btn-secondary" style="margin-top:10px; width:180px;margin-right:10px;">
                                ir al listado de equipos
                            </button>
                        </a>
                        <button type="submit" class="btn btn-primary" style="margin-top:10px;width:180px;margin-right:10px;">
                            Guardar Equipo
                        </button>
                        <br><br><br>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
