<div class="card">
    <div class="card-header">
        <i class="fas fa-file-invoice"></i> <?php echo $titulo; ?>
    </div>
    <div class="card-body">
        <form name="formulario" id="formulario" method="post" action="javascript: enviarPlanPago();" enctype="multipart/form-data">
            <!-- Campos ocultos -->
            <input type="hidden" id="tokenMatricula" name="tokenMatricula" value="<?php echo @$token; ?>">
            <input type="hidden" id="tokenPlanPago" name="tokenPlanPago" value="<?php echo @$result7->token; ?>">
            <input type="hidden" id="hperiodop" name="hperiodop" value="<?php echo @$result7->periodo; ?>">
            <input type="hidden" id="hdiap" name="hdiap" value="<?php echo @$result7->dia_pago; ?>">
            <input type="hidden" id="hppp" name="hppp" value="<?php echo @$result7->publico_privado; ?>">
            <div class="row col-sm-12">
                <!-- Campo: Nombre del Plan de Pago -->
                <div class="col-lg-4 form-group">
                    <label class="col-form-label"><span class="obligatorio">*</span>Nombre del Plan de Pago:</label>
                    <input type="text" class="form-control" required name="nombre" id="nombre" placeholder="Ingresa nombre" value="<?php echo @$result7->nombre; ?>" onBlur="elimina_slash(this); elimina_comillas(this); elimina_blancos_inicio_fin(this);" maxlength="255">
                </div>
                <!-- Campo: Periodo de Pago -->
                <div class="col-lg-4 form-group">
                    <label class="col-form-label"><span class="obligatorio">*</span>Periodo de Pago: 
                        <i class="fas fa-question-circle" data-toggle="tooltip" title="" data-original-title="Cada cuanto se debe pagar" style="color:#20a8d8;"></i>
                    </label>
                    <select id="periodo" name="periodo" class="form-control" onchange="mostrarFechas()">
                        <option value="mensual" <?php echo (@$result7->periodo == 'mensual') ? 'selected' : ''; ?>>Mensual</option>
                        <option value="semestral" <?php echo (@$result7->periodo == 'semestral') ? 'selected' : ''; ?>>Semestral</option>
                        <option value="anual" <?php echo (@$result7->periodo == 'anual') ? 'selected' : ''; ?>>Anual</option>
                    </select>
                </div>
                <!-- Campo: Valor Período -->
                <div class="col-lg-4 form-group">
                    <label class="col-form-label"><span class="obligatorio">*</span>Valor Período:</label>
                    <input type="text" class="form-control" name="valor" id="valor" required placeholder="Ingresa valor" value="<?php echo @$result7->valor; ?>" onBlur="elimina_slash(this); elimina_comillas(this); elimina_blancos_inicio_fin(this); elimina_puntos(this); solo_numeros(this);" maxlength="255">
                </div>
                <!-- Campo: Día de Pago -->
                <div class="col-lg-4 form-group">
                    <label class="col-form-label"><span class="obligatorio">*</span>Día de Pago: 
                        <i class="fas fa-question-circle" data-toggle="tooltip" title="" data-original-title="Día del mes en el cual se realizará el cobro de la Matrícula en caso semestral o anual." style="color:#20a8d8;"></i>
                    </label>
                    <select id="diap" name="diap" class="form-control">
                        <option value="1" <?php echo (@$result7->dia_pago == 1) ? 'selected' : ''; ?>>1</option>
                        <option value="5" <?php echo (@$result7->dia_pago == 5) ? 'selected' : ''; ?>>5</option>
                        <option value="10" <?php echo (@$result7->dia_pago == 10) ? 'selected' : ''; ?>>10</option>
                        <option value="15" <?php echo (@$result7->dia_pago == 15) ? 'selected' : ''; ?>>15</option>
                        <option value="20" <?php echo (@$result7->dia_pago == 20) ? 'selected' : ''; ?>>20</option>
                        <option value="25" <?php echo (@$result7->dia_pago == 25) ? 'selected' : ''; ?>>25</option>
                    </select>
                </div>
                <!-- Campo: Público/Privado -->
                <div class="col-lg-4 form-group">
                    <label class="col-form-label"><span class="obligatorio">*</span>Público/Privado: 
                        <i class="fas fa-question-circle" data-toggle="tooltip" title="" data-original-title="Privada: Sólo será visible por los administradores, quienes podrán matricular vía administración al socio. Pública: los socios se pueden matricular bajo esta modalidad, esperando solo la confirmación de su plan asignado." style="color:#20a8d8;"></i>
                    </label>
                    <select id="ppp" name="ppp" class="form-control">
                        <option value="privado" <?php echo (@$result7->publico_privado == 'privado') ? 'selected' : ''; ?>>Privado</option>
                        <option value="publico" <?php echo (@$result7->publico_privado == 'publico') ? 'selected' : ''; ?>>Público</option>
                    </select>
                </div>
                <!-- Campos de Fecha Inicial y Final (Ocultos por defecto) -->
                <div id="contenedor-fechas" style="display: none;" class="col-lg-12 form-group">
                    <div class="row">
                        <div class="col-lg-6 form-group">
                            <label class="col-form-label"><span class="obligatorio">*</span>Primera Fecha Cierre :</label>
                            <input type="date" class="form-control" name="fecha_cierre1" id="fecha_cierre1" value="<?php echo !empty($result7->fecha_cierre1) ? $result7->fecha_cierre1 : ''; ?>">
                        </div>
                        <div class="col-lg-6 form-group">
                            <label class="col-form-label"><span class="obligatorio">*</span>Segunda Fecha Cierre :</label>
                            <input type="date" class="form-control" name="fecha_cierre2" id="fecha_cierre2" value="<?php echo !empty($result7->fecha_cierre2) ? $result7->fecha_cierre2 : ''; ?>">
                        </div>
                    </div>
                </div>
                <!-- Botones de acción -->
                <div class="col-lg-12 form-group text-center">
                    <br><br>
                    <button type="button" class="btn btn-secondary" onClick="document.location.href='index.php?component=plan&view=plan_list';"> << Volver </button>
                    &nbsp; &nbsp;
                    <button type="submit" class="btn btn-primary"> Guardar </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Script para mostrar u ocultar los campos de fecha -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Mostrar/ocultar campos de fecha al cargar la página
    mostrarFechas();

    // Función para mostrar u ocultar los campos de fecha
    function mostrarFechas() {
        // Obtener el valor seleccionado en el campo "Periodo de Pago"
        var periodo = document.getElementById("periodo").value;

        // Obtener el contenedor de las fechas
        var contenedorFechas = document.getElementById("contenedor-fechas");

        // Obtener los campos de fecha inicial y final
        var fechaInicial = document.getElementById("fecha_cierre1");
        var fechaFinal = document.getElementById("fecha_cierre2");

        // Mostrar u ocultar el contenedor de fechas según el periodo seleccionado
        if (periodo === "semestral") {
            contenedorFechas.style.display = "block";
            fechaInicial.style.display = "block"; // Mostrar fecha inicial
            fechaFinal.style.display = "block";  // Mostrar fecha final
        } else if (periodo === "anual") {
            contenedorFechas.style.display = "block";
            fechaInicial.style.display = "block"; // Mostrar fecha inicial
            fechaFinal.style.display = "none";   // Ocultar fecha final
        } else {
            contenedorFechas.style.display = "none"; // Ocultar todo el contenedor
        }
    }
});
</script>