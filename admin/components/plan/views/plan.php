<div class="card">
    <div class="card-header">
        <i class="fas fa-file-invoice"></i> Plan
    </div>
    <div class="card-body">
        <form name="formulario" id="formulario" method="post" action="javascript: enviar();" enctype="multipart/form-data">
            <!-- Campos ocultos -->
            <input type="hidden" id="token" name="token" value="<?php echo @$token; ?>">
            <input type="hidden" id="htipo" name="htipo" value="<?php echo @$result7->tipo; ?>">
            <input type="hidden" id="hdia" name="hdia" value="<?php echo @$result7->dia_pago_1; ?>">
            <input type="hidden" id="hpp" name="hpp" value="<?php echo @$result7->publica_privada; ?>">

            <div class="row col-sm-12">
                <!-- Campo: Nombre del Plan -->
                <div class="col-lg-4 form-group">
                    <label class="col-form-label"><span class="obligatorio">*</span>Nombre del Plan:</label>
                    <input type="text" class="form-control" required name="nombre" id="nombre" placeholder="Ingresa nombre" value="<?php echo @$result7->nombre; ?>" onBlur="elimina_slash(this); elimina_comillas(this); elimina_blancos_inicio_fin(this);" maxlength="255">
                </div>

                <!-- Campo: Tipo de Matrícula -->
                <div class="col-lg-4 form-group">
                    <label class="col-form-label"><span class="obligatorio">*</span>Tipo de Matrícula: 
                        <i class="fas fa-question-circle" data-toggle="tooltip" title="" data-original-title="Matrícula única es cuando se paga solo una vez." style="color:#20a8d8;"></i>
                    </label>
                    <select id="tipo" name="tipo" class="form-control">
                        <option value="unica">Única</option>
                        <option value="semestral">Semestral</option>
                        <option value="anual">Anual</option>
                    </select>
                </div>

                <!-- Campo: Valor Matrícula -->
                <div class="col-lg-4 form-group">
                    <label class="col-form-label"><span class="obligatorio">*</span>Valor Matrícula:</label>
                    <input type="text" class="form-control" name="valor" id="valor" required placeholder="Ingresa valor" value="<?php echo @$result7->valor; ?>" onBlur="elimina_slash(this); elimina_comillas(this); elimina_blancos_inicio_fin(this); elimina_puntos(this); solo_numeros(this);" maxlength="255">
                </div>

                <!-- Campo: Día de Pago -->
                <div class="col-lg-4 form-group">
                    <label class="col-form-label"><span class="obligatorio">*</span>Día de Pago: 
                        <i class="fas fa-question-circle" data-toggle="tooltip" title="" data-original-title="Día del mes en el cual se realizará el cobro de la Matrícula en caso semestral o anual." style="color:#20a8d8;"></i>
                    </label>
                    <select id="dia" name="dia" class="form-control">
                        <option value="1">1</option>
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="20">20</option>
                        <option value="25">25</option>
                    </select>
                </div>

                <!-- Campo: Pública/Privada -->
                <div class="col-lg-4 form-group">
                    <label class="col-form-label"><span class="obligatorio">*</span>Pública/Privada: 
                        <i class="fas fa-question-circle" data-toggle="tooltip" title="" data-original-title="Privada: Sólo será visible por los administradores, quienes podrán matricular vía administración al socio. Pública: los socios se pueden matricular bajo esta modalidad, esperando solo la confirmación de su plan asignado." style="color:#20a8d8;"></i>
                    </label>
                    <select id="pp" name="pp" class="form-control">
                        <option value="privada">Privada</option>
                        <option value="publica">Pública</option>
                    </select>
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