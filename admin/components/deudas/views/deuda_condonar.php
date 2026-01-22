<?php
session_start();
include("../../../configuration.php");
include("../../../includes/conexionMysql.php");
include("../../../includes/funciones.php");

// Obtener parámetros
$token = isset($_GET['token']) ? $_GET['token'] : '';
$accion = isset($_GET['accion']) ? $_GET['accion'] : '';

// Validar acción
if (!in_array($accion, ['eliminar', 'condonar'])) {
    header("Location: index.php?component=deudas&view=deudas_list");
    exit();
}

// Configurar textos según acción
$titulo = ($accion == 'eliminar') ? 'Eliminación' : 'Condonación';
$boton = ucfirst($accion); // Capitaliza la primera letra
$icono = ($accion == 'eliminar') ? 'fa-trash' : 'fa-clipboard-check';

// Mensajes y configuración
$mensaje = isset($_SESSION['deuda_mensaje']) ? $_SESSION['deuda_mensaje'] : '';
unset($_SESSION['deuda_mensaje']);
?>

<div class="card">
    <div class="card-header"><i class="fas <?php echo $icono; ?>"></i> <?php echo $titulo; ?> de Deuda</div>
    <div class="card-body">
        <form name="formulario" id="formulario" method="post" action="javascript: ejecutarAccion();" enctype="multipart/form-data">
            <input id="token" name="token" type="hidden" value="<?php echo $token; ?>">
            <input id="accion" name="accion" type="hidden" value="<?php echo $accion; ?>">

            <div class="col-sm-12">
                <div class="row">
                    <div class="col-lg-12 form-group"> 
                        <label class="col-form-label"><span class="obligatorio">*</span>Motivo de la <?php echo $accion; ?>:</label>
                        <input type="text" class="form-control" name="observacion" id="observacion" placeholder="Motivo" value="" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);" maxlength="255">
                    </div>

                    <div class="col-lg-12 text-center">
                        <br>
                        <a href="index.php?component=deudas&view=deudas_list">
                            <button type="button" class="btn btn-secondary" style="margin-top:10px; width:180px;margin-right:10px;">Ir al listado de deudas</button>
                        </a>
                        
                        <button type="submit" class="btn btn-primary" style="margin-top:10px;width:180px;margin-right:10px;">
                            <?php echo $boton; ?> deuda
                        </button>
                        <br><br><br> 
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function ejecutarAccion() {
    var accion = document.getElementById('accion').value;
    
    if (accion === 'eliminar') {
        eliminarD();
    } else if (accion === 'condonar') {
        condonar();
    }
}
</script>