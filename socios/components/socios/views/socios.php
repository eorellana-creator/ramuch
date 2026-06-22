<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Agrega scroll horizontal a todo el body en móviles
if (window.innerWidth <= 768) {
    document.body.style.overflowX = 'auto';
    document.body.style.minWidth = '1000px';
}
</script>

<style>
/* HABILITAR SCROLL HORIZONTAL EN TODO EL CONTENIDO */
@media (max-width: 768px) {
    html, body {
        overflow-x: auto !important;
        max-width: 100vw !important;
    }
    
    .card-body {
        min-width: 1000px !important;
    }
    
    /* Asegurar que los contenedores no limiten el ancho */
    .container, .container-fluid {
        min-width: 1000px !important;
        overflow-x: visible !important;
    }
    
    /* Permitir que la pestaña se expanda */
    .tab-content, .tab-pane {
        min-width: 1000px !important;
    }
}
</style>

<div class="card" style="overflow-x: auto; min-width: 1000px;">
    <div class="card-header">
        <i class="fas fa-user"></i> Socio Ramuch <?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>". @$result5->nombre . "</strong>";?>
    </div>
    <div class="card-body">
        <?php echo $actualizado;?>

        <!-- Navigation Tabs -->
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <a class="nav-item nav-link active tab-datos" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="true">Datos</a>
                <a <?php echo $oculta_tabs;?> class="nav-item nav-link" id="nav-medical-tab" data-toggle="tab" href="#nav-medical" role="tab" aria-controls="nav-medical" aria-selected="false">Médico</a>
                <a <?php echo $oculta_tabs;?> class="nav-item nav-link" id="nav-finance-tab" data-toggle="tab" href="#nav-finance" role="tab" aria-controls="nav-finance" aria-selected="false">Financiero</a>
                <a <?php echo $oculta_tabs;?> class="nav-item nav-link tab-inscripcion" id="nav-inscripcion-tab" data-toggle="tab" href="#nav-inscripcion" role="tab" aria-controls="nav-inscripcion" aria-selected="false">Tipo Inscripción</a>
                <a <?php echo $oculta_tabs;?> class="nav-item nav-link tab-prestamo" id="nav-prestamo-tab" data-toggle="tab" href="#nav-prestamo" role="tab" aria-controls="nav-prestamo" aria-selected="false">Historial de Préstamos</a>
                <a <?php echo $oculta_tabs;?> class="nav-item nav-link tab-pass" id="nav-password-tab" data-toggle="tab" href="#nav-password" role="tab" aria-controls="nav-password" aria-selected="false">Cambiar Password</a>
            </div>
        </nav>

        <!-- Form -->
        <form name="formulario" id="formulario" method="post" action="javascript: enviar();" enctype="multipart/form-data">
            <input type="hidden" id="token" name="token" value="<?php echo @$token;?>">

            <!-- Tab Content -->
            <div class="tab-content" id="nav-tabContent">

                <!-- Datos Personales Tab -->
                <div class="tab-pane fade show active" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                    <div class="container">
                        <div class="row">
                            <h4>Datos Personales</h4>
                            <div class="linea-horizontal"></div>

                            <div class="col-sm-3 text-center">
                                <img class="image-profile-border" id="foto-perfil" src="<?php echo @$imagen_perfil;?>" />
                                <br><br>
                                <input id="foto" name="foto" type="file" onChange="subirImagen();" class="btn btn-success btn-xs" style="display: none;" />
                                <input type="button" value="Subir imagen..." onclick="document.getElementById('foto').click();" <?php echo $oculta_tabs;?> />
                            </div>

                            <div class="col-sm-9">
                                <div class="row">
                                    <div class="col-lg-6 form-group">
                                        <label class="col-form-label"><span class="obligatorio">*</span>Nombre:</label>
                                        <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Ingresa nombre" value="<?php echo @$result5->nombre;?>" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);" maxlength="255">
                                    </div>

                                    <div class="col-lg-3 form-group">
                                        <label class="col-form-label"><span class="obligatorio">*</span>Rut:</label>
                                        <input type="text" class="form-control" readonly name="rut" id="rut" placeholder="Rut" value="<?php echo @$result5->rut;?>" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);">
                                        <div id="errorrut2" class="errorcampo"></div>
                                    </div>

                                    <div class="col-lg-3 form-group">
                                        <label class="col-form-label">Fecha Nac.:</label>
                                        <input class="form-control" id="fechaNacimiento" type="date" name="fechaNacimiento" id="fechaNacimiento" placeholder="date" value="<?php echo @$result5->fecha_nacimiento;?>">
                                    </div>

                                    <div class="col-lg-6 form-group">
                                        <label class="col-form-label"><span class="obligatorio">*</span>Correo:</label>
                                        <input type="email" class="form-control" name="mail" id="mail" readonly placeholder="Correo" value="<?php echo @$result5->mail;?>" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);" maxlength="255">
                                    </div>
                                    
                                    <div class="col-lg-6 form-group">
                                        <label class="col-form-label">Telefono:</label>
                                        <input type="text" class="form-control" name="fono" id="fono" placeholder="Teléfono" value="<?php echo @$result5->fono;?>" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);">
                                    </div>

                                    <div class="col-lg-6 form-group">
                                        <label class="col-form-label">Direccion: <?php echo @$link_mapa;?></label>
                                        <input type="text" class="form-control" name="direccion" id="direccion" placeholder="Dirección" value="<?php echo @$result5->direccion;?>" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);">
                                    </div>
                                </div>
                            </div>

                            <h4>Contacto de Emergencia</h4>
                            <div class="linea-horizontal"></div>

                            <div class="col-lg-6 form-group">
                                <label class="col-form-label">Nombre:</label>
                                <input type="text" class="form-control" name="nombreContacto" id="nombreContacto" placeholder="Ingresa nombre" value="<?php echo @$result5->nombre_contacto;?>" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);" maxlength="255">
                            </div>

                            <div class="col-lg-3 form-group">
                                <label class="col-form-label">Telefono:</label>
                                <input type="text" class="form-control" name="fonoContacto" id="fonoContacto" placeholder="Teléfono" value="<?php echo @$result5->fono_contacto;?>" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);">
                            </div>

                            <div class="col-lg-3 form-group">
                                <label class="col-form-label">Correo:</label>
                                <input type="email" class="form-control" name="mailContacto" id="mailContacto" placeholder="Correo" value="<?php echo @$result5->mail_contacto;?>" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);" maxlength="255">
                            </div>

                            <h4>Estudios</h4>
                            <div class="linea-horizontal"></div>

                            <div class="col-lg-6 form-group">
                                <label class="col-form-label">Carrera:</label>
                                <input type="text" class="form-control" name="carrera" id="carrera" placeholder="Ingresa Carrera" value="<?php echo @$result5->carrera;?>" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);" maxlength="255">
                            </div>

                            <div class="col-lg-6 form-group">
                                <label class="col-form-label">Institución:</label>
                                <input type="text" class="form-control" name="institucion" id="institucion" placeholder="Ingresa Institucion" value="<?php echo @$result5->institucion;?>" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);" maxlength="255">
                            </div>

                            <div class="col-lg-6 form-group">
                                <label class="col-form-label">Estado:</label>
                                <select class="form-control" id="estado_estudios" name="estado_estudios">
                                    <?php echo @$estado_estudios;?>
                                    <option value="0">Cursando</option>
                                    <option value="1">Congelado</option>
                                    <option value="2">Egresado</option>
                                    <option value="3">Titulado</option>
                                    <option value="99">No informado</option>
                                </select>
                            </div>

                            <div class="col-lg-3 form-group">
                                <label class="col-form-label">Años Cursados:</label>
                                <select class="form-control" id="anos_cursados" name="anos_cursados">
                                    <?php echo @$anos_cursados;?>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                </select>
                            </div>

                            <div class="col-lg-3 form-group">
                                <label class="col-form-label">Duración de la Carrera:</label>
                                <select class="form-control" id="anos_carrera" name="anos_carrera">
                                    <?php echo @$anos_carrera;?>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                </select>
                            </div>

                            <div class="col-lg-3 form-group">
                                <label class="col-form-label">Certificado de Estudiante:</label>
                                <input id="archivo" name="archivo" type="file" onChange="validaCertificado(this);" class="btn btn-success btn-xs" />
                                <div><?php echo @$certificado;?></div>
                            </div>

                            <div class="col-lg-3 form-group">
                                <label class="col-form-label">Fecha Caducidad Certificado:</label>
                                <input class="form-control" type="date" name="fechaCertificado" id="fechaCertificado" placeholder="date" value="<?php echo @$result5->certificado_vencimiento;?>">
                            </div>

                            <?php echo $oculta_inscripcion_primera_vez;?>
                            <?php echo $campos_password_nuevos;?>
                        </div>
                    </div>

                    <div class="col-lg-12 text-center">
                        <br>
                        &nbsp; &nbsp;
                        <button type="submit" class="btn btn-primary"> Guardar datos Personales</button>
                        <br><br><br>
                    </div>
                </div>

                <!-- Información Médica Tab -->
                <div class="tab-pane fade" id="nav-medical" role="tabpanel" aria-labelledby="nav-medical-tab">
                    <div class="container">
                        <div class="row">
                            <h4>Antecedentes Médicos</h4>
                            <div class="linea-horizontal"></div>

                            <div class="col-sm-2 text-center">
                                <i class="fas fa-notes-medical icono-salud"></i>
                            </div>

                            <div class="col-sm-10">
                                <div class="row">
                                    <div class="col-lg-4 form-group">
                                        <label class="col-form-label">Tipo de sangre:</label>
                                        <select id="sangre" name="sangre" class="form-control">
                                            <?php echo @$tipo_sangre;?>
                                            <option value="A+">A+</option>
                                            <option value="O+">O+</option>
                                            <option value="B+">B+</option>
                                            <option value="AB+">AB+</option>
                                            <option value="A-">A-</option>
                                            <option value="O-">O-</option>
                                            <option value="B-">B-</option>
                                            <option value="AB-">AB-</option>
                                        </select>
                                    </div>

                                    <div class="col-lg-4 form-group">
                                        <label class="col-form-label">Enfermedades Crónicas:</label>
                                        <textarea id="enfermedades" name="enfermedades" class="form-control" placeholder="Ingresa tus enfermedades crónicas" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);"><?php echo @$result5->enfermedades;?></textarea>
                                    </div>

                                    <div class="col-lg-4 form-group">
                                        <label class="col-form-label">Operaciones:</label>
                                        <textarea id="operaciones" name="operaciones" class="form-control" placeholder="Ingresa tus operaciones" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);"><?php echo @$result5->operaciones;?></textarea>
                                    </div>

                                    <div class="col-lg-4 form-group">
                                        <label class="col-form-label">Alergias:</label>
                                        <textarea id="alergias" name="alergias" class="form-control" placeholder="Ingresa tus operaciones" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);"><?php echo @$result5->alergias;?></textarea>
                                    </div>

                                    <div class="col-lg-4 form-group">
                                        <label class="col-form-label">Medicamentos Recurrentes:</label>
                                        <textarea id="medicamentos" name="medicamentos" class="form-control" placeholder="Ingresa tus medicamentos" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);"><?php echo @$result5->medicamentos;?></textarea>
                                    </div>

                                    <div class="col-lg-4 form-group">
                                        <label class="col-form-label">Otros Diagnósticos de Interés: <?php echo @$link_mapa;?></label>
                                        <textarea id="diagnosticos" name="diagnosticos" class="form-control" placeholder="Ingresa tus diagnosticos" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);"><?php echo @$result5->otros_diagnosticos;?></textarea>
                                    </div>

                                    <div class="col-lg-4 form-group">
                                        <label class="col-form-label">Previsión:</label>
                                        <input type="text" class="form-control" name="prevision" id="prevision" placeholder="Ingresa la Isapre o Fonasa" value="<?php echo @$result5->prevision_salud;?>" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);" maxlength="255">
                                    </div>

                                    <div class="col-lg-4 form-group">
                                        <label class="col-form-label">Preferencia de Atención:</label>
                                        <input type="text" class="form-control" name="preferencia" id="preferencia" placeholder="Ej: Clínica Indisa" value="<?php echo @$result5->prevision_salud;?>" onBlur="elimina_slash(this);elimina_comillas(this);elimina_blancos_inicio_fin(this);" maxlength="255">
                                    </div>

                                    <div class="col-lg-4 form-group">
                                        <label class="col-form-label">Donante de Órganos</label>
                                        <select id="donante" name="donante" class="form-control">
                                            <?php echo @$donante;?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12 text-center">
                        <br>
                        &nbsp; &nbsp;
                        <button type="submit" class="btn btn-primary"> Guardar datos Médicos</button>
                        <br><br><br>
                    </div>
                </div>

                <!-- Información Financiera Tab -->
                <div class="tab-pane fade" id="nav-finance" role="tabpanel2" aria-labelledby="nav-finance-tab">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#balance" role="tab" aria-controls="balance">Balance</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link tab-pagos" data-toggle="tab" href="#pagos" role="tab" aria-controls="pagos">Pagos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link tab-deudas" data-toggle="tab" href="#deudas" role="tab" aria-controls="deudas">Deudas</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- Balance Tab -->
                        <div class="tab-pane active" id="balance" role="tabpanel">
                            <div class="row">
                                <div class="col-6 col-lg-3">
                                    <div class="card" style="cursor:pointer;" onClick="$('.tab-pagos').click();">
                                        <div class="card-body p-3 d-flex align-items-center">
                                            <i class="fa fa-hand-holding-usd bg-primary p-3 font-2xl mr-3"></i>
                                            <div>
                                                <div class="text-value-sm text-primary">$<?php echo $total_pagos;?>.-</div>
                                                <div class="text-muted text-uppercase font-weight-bold small">Movimientos de Pagos</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6 col-lg-3">
                                    <div class="card" style="cursor:pointer;" onClick="$('.tab-deudas').click();">
                                        <div class="card-body p-3 d-flex align-items-center">
                                            <i class="fas fa-search-dollar bg-danger p-3 font-2xl mr-3"></i>
                                            <div>
                                                <div class="text-value-sm text-danger">$<?php echo $total_deudas;?>.-</div>
                                                <div class="text-muted text-uppercase font-weight-bold small">Deudas</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pagos Tab -->
                        <div class="tab-pane" id="pagos" role="tabpanel">
                            <div class="card">
                                <div class="card-header">
                                    <i class="fa fa-align-justify"></i> Pagos
                                </div>
                                <div class="card-body">
                                    <table id="tablapagos" class="display" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>ID Movimiento</th>
                                                <th>Fecha</th>
                                                <th>Medio de Pago</th>
                                                <th>Glosa</th>
                                                <th>Valor</th>
                                            </tr>
                                        </thead>
                                        <?php echo $datos_pagos;?>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Deudas Tab -->
                        <div class="tab-pane" id="deudas" role="tabpanel">
                            <div class="card">
                                <div class="card-header">
                                    <i class="fa fa-align-justify"></i> Deudas
                                </div>
                                <div class="card-body">
                                    <table id="tabladeudas" class="display" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>ID Movimiento</th>
                                                <th>Fecha</th>
                                                <th>Tipo de Deuda</th>
                                                <th>Observación</th>
                                                <th>Valor</th>
                                            </tr>
                                        </thead>
                                        <?php echo $datos_deudas;?>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Historial de Préstamos Tab - CON SCROLL HORIZONTAL SIMPLE -->
                <div class="tab-pane fade" id="nav-prestamo" role="tabpanel" aria-labelledby="nav-prestamo-tab">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <i class="fa fa-align-justify"></i> Historial de Préstamos 
                                        <a href="index.php?component=equipo&view=equipo_list">
                                            <button type="button" class="btn btn-primary btn-sm">Pedir equipo</button>
                                        </a>
                                        <button type="button" class="btn btn-warning btn-sm ml-2" onclick="cargarAtrasos()">
                                            Ver listado de atrasos
                                        </button>
                                    </div>
                                    <div class="card-body p-0"> <!-- Sin padding -->
                                        <!-- CONTENEDOR CON SCROLL HORIZONTAL -->
                                        <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                                            <table id="tablaprestamos" class="table mb-0" style="width:100%; min-width: 1000px;">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th></th>
                                                        <th>Equipo</th>
                                                        <th>Fecha Préstamo</th>
                                                        <th>Fecha compromiso devolución</th>
                                                        <th>Fecha efectiva de devolución</th>
                                                        <th>Responsables</th>
                                                        <th>Observación</th>
                                                        <th>Estado de la devolución</th>
                                                        <th>Estado Solicitud</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php echo $datos_prestamo;?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <!-- FIN CONTENEDOR CON SCROLL -->
                                        
                                        <!-- Nota para móviles (opcional) -->
                                        <div class="alert alert-info d-md-none m-3">
                                            <small>
                                                <i class="fas fa-mobile-alt mr-2"></i>
                                                <strong>Nota:</strong> Desliza horizontalmente para ver todas las columnas.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Password Tab -->
                <div class="tab-pane fade" id="nav-password" role="tabpanel" aria-labelledby="nav-password-tab">
                    <div class="container">
                        <div class="row">
                            <h4>Contraseña.</h4>
                            <div class="linea-horizontal"></div>

                            <div class="col-sm-2 text-center">
                                <i class="fas fa-key icono-password"></i>
                            </div>

                            <div class="col-sm-10">
                                <?php echo $campos_password;?>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12 text-center">
                        <br>
                        &nbsp; &nbsp;
                        <button type="button" class="btn btn-primary" onClick="savePassword();"> Guardar Contraseña </button>
                        <br><br><br>
                    </div>
                </div>

                <!-- Inscripción Tab -->
                <div class="tab-pane fade" id="nav-inscripcion" role="tabpanel" aria-labelledby="nav-inscripcion-tab">
                    <div class="container">
                        <div class="row">
                            <h4>Tipo Inscripción <?php echo @$tipo_inscripcion;?></h4>
                            <div class="linea-horizontal"></div>

                            <div class="col-sm-2 text-center">
                                <i class="fas fa-user icono-password"></i>
                            </div>

                            <div class="col-sm-10">
                                <div class="row">
                                    <input type="hidden" id="hplan" name="hplan" value="<?php echo @$result5->id_plan_matricula;?>">

                                    <div class="col-lg-4 form-group">
                                        <label class="col-form-label">Tipo de Inscripción </label>
                                        <?php echo $tipo_inscripcion;?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12 text-center">
                        <br>
                        &nbsp; &nbsp;
                        <br><br><br>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modals -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-warning" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Rut ya existe</h4>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span>×</span>
                </button>
            </div>
            <div class="modal-body">
                <p>El Rut que intentas ingresar ya existe en el sistema. Se encuentra registrado en el listado de socios.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-warning" type="button" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModal2Label">
    <div class="modal-dialog modal-warning" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Correo ya existe</h4>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span>×</span>
                </button>
            </div>
            <div class="modal-body">
                <p>El Correo que intentas ingresar ya existe en el sistema. Se encuentra registrado en el listado de socios.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-warning" type="button" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para solicitar extensión (modificado para múltiples productos) -->
<div class="modal fade" id="modalExtension" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalExtensionTitle">Solicitar Extensión</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadingExtension" class="text-center" style="display:none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                    <p>Verificando extensiones disponibles...</p>
                </div>
                
                <form id="formExtension" style="display:none;">
                    <input type="hidden" id="tipoExtension" name="tipoExtension" value="1">
                    
                    <!-- Lista de productos en préstamo -->
                    <div class="form-group">
                        <label class="font-weight-bold">Seleccione los productos para extensión:</label>
                        
                        <!-- Checkbox "Seleccionar Todos" -->
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="selectAll">
                            <label class="form-check-label font-weight-bold" for="selectAll">
                                Seleccionar Todos
                            </label>
                        </div>
                        
                        <!-- Lista de productos -->
                        <div id="listaProductos" class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                            <!-- Los productos se cargarán aquí dinámicamente -->
                        </div>
                        <small class="form-text text-muted">
                            Seleccione los productos para los cuales desea solicitar extensión
                        </small>
                    </div>
                    
                    <div class="form-group">
                        <label for="nuevaFecha">Nueva fecha de devolución:</label>
                        <input type="date" class="form-control" id="nuevaFecha" name="nuevaFecha" min="" required>
                    </div>
                    <div class="form-group">
                        <label for="motivo">Motivo de la extensión:</label>
                        <textarea class="form-control" id="motivo" name="motivo" rows="3" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" id="btnEnviarExtension" class="btn btn-primary" onclick="enviar_Solicitud_Extension()">
                    Solicitar Extensión
                </button>
            </div>
        </div>
    </div>
</div>

<!-- NUEVO MODAL PARA ATRASOS -->
<div class="modal fade" id="modalAtrasos" tabindex="-1" role="dialog" aria-labelledby="modalAtrasosLabel" >
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="modalAtrasosLabel">Listado de Préstamos con Atraso</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadingAtrasos" class="text-center">
                    <div class="spinner-border text-warning" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                    <p>Cargando préstamos con atraso...</p>
                </div>
                <div id="contenidoAtrasos" style="display: none;">
                    <table class="table table-striped table-bordered" id="tablaAtrasos">
                        <thead class="thead-dark">
                            <tr>
                                <th>Equipo</th>
                                <th>Fecha Préstamo</th>
                                <th>Fecha compromiso devolución</th>
                                <th>Fecha efectiva devolución</th>
                                <th>Días de atraso</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody id="cuerpoTablaAtrasos">
                            <!-- Los datos se cargarán aquí dinámicamente -->
                        </tbody>
                    </table>
                </div>
                <div id="sinAtrasos" class="alert alert-info text-center" style="display: none;">
                    No hay préstamos con atraso.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>



<!-- jQuery DEBE ir primero -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    
// === SCRIPT CORREGIDO - NOMBRES ACTUALIZADOS ===
console.log('= INICIANDO SCRIPT =');

// Verificar jQuery
if (typeof jQuery === 'undefined') {
    console.error('❌ jQuery no está cargado');
} else {
    console.log('✅ jQuery cargado correctamente');
}

// 🔥 NOMBRES CAMBIADOS PARA EVITAR CONFLICTOS
window.sol_ext2 = function(token) {
    console.log('🎯 sol_ext2 EJECUTADA - Token:', token);
    try {
        $('#tipoExtension').val('2');
        $('#modalExtensionTitle').text('Solicitar Segunda Extensión');
        abrirModalExtension(token);
    } catch (error) {
        console.error('❌ ERROR en sol_ext2:', error);
    }
};

window.sol_ext1 = function(token) {
    console.log('🎯 sol_ext1 EJECUTADA - Token:', token);
    try {
        $('#tipoExtension').val('1');
        $('#modalExtensionTitle').text('Solicitar Primera Extensión');
        abrirModalExtension(token);
    } catch (error) {
        console.error('❌ ERROR en sol_ext1:', error);
    }
};

// Función para cargar los atrasos
function cargarAtrasos() {
    $('#modalAtrasos').modal('show');
    $('#loadingAtrasos').show();
    $('#contenidoAtrasos').hide();
    $('#sinAtrasos').hide();
    
    $.ajax({
        url: 'components/socios/models/atrasos.php',
        type: 'POST',
        data: { action: 'obtenerAtrasos' },
        dataType: 'json',
        success: function(response) {
            $('#loadingAtrasos').hide();
            if (response.success) {
                if (response.data && response.data.length > 0) {
                    $('#cuerpoTablaAtrasos').empty();
                    response.data.forEach(function(prestamo) {
                        var fila = '<tr>' +
                            '<td>' + (prestamo.equipo || 'N/A') + '</td>' +
                            '<td>' + (prestamo.fecha_prestamo || 'N/A') + '</td>' +
                            '<td>' + (prestamo.fecha_compromiso_devolucion || 'N/A') + '</td>' +
                            '<td>' + (prestamo.fecha_efectiva_devolucion || 'Pendiente') + '</td>' +
                            '<td>' + (prestamo.dias_atraso || 'N/A') + '</td>' +
                            '<td class="' + (prestamo.clase_estado || '') + '">' + 
                                '<strong>Devolución:</strong> ' + (prestamo.estado_devolucion || 'N/A') + '<br>' +
                                '<strong>Solicitud:</strong> ' + (prestamo.estado_solicitud || 'N/A') +
                            '</td></tr>';
                        $('#cuerpoTablaAtrasos').append(fila);
                    });
                    $('#contenidoAtrasos').show();
                } else {
                    $('#sinAtrasos').show();
                }
            } else {
                Swal.fire('Error', response.message || 'Error al cargar los atrasos', 'error');
            }
        },
        error: function(xhr, status, error) {
            $('#loadingAtrasos').hide();
            Swal.fire('Error', 'Error de conexión al servidor: ' + error, 'error');
        }
    });
}

// Función para abrir el modal
function abrirModalExtension(token) {
    console.log('🔵 abrirModalExtension - Token:', token);
    
    try {
        limpiarModalExtension();
        $('#modalExtension').modal('show');
        
        $.ajax({
            url: 'components/socios/models/obtener_prestamos_activos.php',
            type: 'POST',
            data: { action: 'obtener_prestamos_activos' },
            dataType: 'json',
            success: function(response) {
                console.log('✅ AJAX Success - Productos:', response.data?.length || 0);
                
                $('#loadingExtension').hide();
                if (response.success) {
                    mostrarListaProductos(response.data);
                    $('#formExtension').show();
                    
                    // Configurar fecha mínima
                    var hoy = new Date().toISOString().split('T')[0];
                    $('#nuevaFecha').attr('min', hoy);
                    $('#nuevaFecha').val('');
                    
                    // Para segunda extensión, ajustar fecha mínima
                    var tipoExtension = $('#tipoExtension').val();
                    if (tipoExtension === '2' && response.data.length > 0) {
                        var fechaPrimeraExtension = obtenerFechaPrimeraExtension(response.data);
                        if (fechaPrimeraExtension) {
                            $('#nuevaFecha').attr('min', fechaPrimeraExtension);
                        }
                    }
                    
                    $('#motivo').val('');
                } else {
                    Swal.fire('Error', response.mensaje, 'error');
                    $('#modalExtension').modal('hide');
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ AJAX Error:', error);
                $('#loadingExtension').hide();
                Swal.fire('Error', 'Error al cargar los préstamos activos', 'error');
                $('#modalExtension').modal('hide');
            }
        });
    } catch (error) {
        console.error('❌ ERROR en abrirModalExtension:', error);
    }
}

// Función para limpiar el modal
function limpiarModalExtension() {
    $('#listaProductos').html('<div class="text-center">Cargando productos...</div>');
    $('#selectAll').prop('checked', false);
    $('#nuevaFecha').val('');
    $('#motivo').val('');
    $('#formExtension').hide();
    $('#loadingExtension').show();
    $('#btnEnviarExtension').prop('disabled', true);
    $('#btnEnviarExtension').html('Solicitar Extensión');
}

// Función para obtener la fecha de la primera extensión
function obtenerFechaPrimeraExtension(productos) {
    var fechaMinima = null;
    productos.forEach(function(producto) {
        if (producto.fecha_propuesta_extension) {
            if (!fechaMinima || producto.fecha_propuesta_extension > fechaMinima) {
                fechaMinima = producto.fecha_propuesta_extension;
            }
        }
    });
    return fechaMinima;
}

// Función para mostrar la lista de productos - SIMPLIFICADA
function mostrarListaProductos(productos) {
    console.log('📋 mostrarListaProductos - Tipo:', $('#tipoExtension').val(), 'Productos:', productos?.length || 0);
    
    $('#listaProductos').html('');
    var listaHtml = '';
    var tipoExtension = $('#tipoExtension').val();
    var productosDisponibles = 0;

    if (productos && productos.length > 0) {
        productos.forEach(function(producto, index) {
            var puedeSolicitar = false;
            var disabled = '';
            var checked = 'checked';
            var textoEstado = '';

            // LÓGICA CORREGIDA PARA PRIMERA EXTENSIÓN
            if (tipoExtension === '1') {
                
                if (producto.extensiones_solicitadas >= 2) {
                    // Límite alcanzado - NO puede solicitar
                    textoEstado = `<small class="text-danger d-block">✗ Límite de extensiones alcanzado</small>`;
                    disabled = 'disabled';
                    checked = '';
                } else if (producto.estado_extension === 'pendiente') {
                    // Ya tiene extensión pendiente - NO puede solicitar otra
                    textoEstado = `<small class="text-warning d-block">⏳ Extensión pendiente</small>`;
                    disabled = 'disabled';
                    checked = '';
                } else if (producto.estado_extension === 'aprobada') {
                    // Ya tiene extensión aprobada - NO puede solicitar primera de nuevo
                    textoEstado = `<small class="text-info d-block">✅ Extensión aprobada</small>`;
                    disabled = 'disabled';
                    checked = '';
                } else {
                    // ✅ Estados: null, undefined, '', 'no solicitada', 'rechazada'
                    // ✅ ESTOS SON LOS PRODUCTOS DISPONIBLES para primera extensión
                    textoEstado = `<small class="text-success d-block">✓ Disponible para primera extensión</small>`;
                    puedeSolicitar = true;
                    productosDisponibles++;
                    disabled = '';
                    checked = 'checked';
                }
            } 
            // LÓGICA PARA SEGUNDA EXTENSIÓN (MANTENER IGUAL)
            else if (tipoExtension === '2') {
                if (producto.estado_extension === 'aprobada' && producto.extensiones_solicitadas < 2) {
                    if (!producto.estado_extension2 || producto.estado_extension2 === 'no solicitada' || producto.estado_extension2 === 'rechazada') {
                        textoEstado = `<small class="text-success d-block">✓ Disponible para 2da extensión</small>`;
                        puedeSolicitar = true;
                        productosDisponibles++;
                        disabled = '';
                        checked = 'checked';
                    } else if (producto.estado_extension2 === 'pendiente') {
                        textoEstado = `<small class="text-warning d-block">⏳ 2da extensión pendiente</small>`;
                        disabled = 'disabled';
                        checked = '';
                    } else if (producto.estado_extension2 === 'aprobada') {
                        textoEstado = `<small class="text-info d-block">✅ 2da extensión aprobada</small>`;
                        disabled = 'disabled';
                        checked = '';
                    }
                } else {
                    textoEstado = `<small class="text-warning d-block">⚠️ No disponible para 2da extensión</small>`;
                    disabled = 'disabled';
                    checked = '';
                }
            }

            // Renderizar producto
            var claseTexto = puedeSolicitar ? '' : 'text-muted';
            listaHtml += `
                <div class="form-check">
                    <input class="form-check-input ${puedeSolicitar ? 'producto-checkbox' : ''}" 
                           type="checkbox" 
                           value="${producto.token}" 
                           id="producto_${index}"
                           ${checked}
                           ${disabled}>
                    <label class="form-check-label ${claseTexto}" for="producto_${index}">
                        <strong>${producto.nombre_equipo}</strong><br>
                        <small class="text-muted">
                            Préstamo: ${producto.fecha_prestamo} | Devolución: ${producto.fecha_debe_devolver}
                        </small>
                        ${textoEstado}
                    </label>
                </div>
                <hr class="my-2">
            `;
        });

        console.log(`🎯 RESUMEN: ${productosDisponibles} productos disponibles de ${productos.length} totales`);

        if (productosDisponibles === 0) {
            listaHtml = `
                <div class="alert alert-warning">
                    <h6>No hay productos disponibles para ${tipoExtension === '1' ? 'primera' : 'segunda'} extensión</h6>
                    <p class="mb-0">Se muestran todos los productos con su estado actual.</p>
                </div>
                ${listaHtml}
            `;
        }
    } else {
        listaHtml = '<div class="alert alert-info">No tiene productos en préstamo actualmente.</div>';
    }
    
    $('#listaProductos').html(listaHtml);
    
    // Configurar "Seleccionar Todos"
    if ($('.producto-checkbox:not(:disabled)').length > 0) {
        $('#selectAll').off('change').on('change', function() {
            var isChecked = $(this).prop('checked');
            $('.producto-checkbox:not(:disabled)').prop('checked', isChecked);
            actualizarEstadoBotonEnviar();
        });
        $('#selectAll').prop('disabled', false);
        
        $('.producto-checkbox').off('change').on('change', function() {
            var todosCheckeados = $('.producto-checkbox:not(:disabled)').length === 
                                 $('.producto-checkbox:not(:disabled):checked').length;
            $('#selectAll').prop('checked', todosCheckeados);
            actualizarEstadoBotonEnviar();
        });
        
        $('#selectAll').prop('checked', 
            $('.producto-checkbox:not(:disabled)').length === 
            $('.producto-checkbox:not(:disabled):checked').length
        );
    } else {
        $('#selectAll').prop('disabled', true);
        $('#selectAll').prop('checked', false);
    }
    
    actualizarEstadoBotonEnviar();
}

// Función auxiliar para actualizar el estado del botón de enviar
function actualizarEstadoBotonEnviar() {
    var hayProductosSeleccionados = $('.producto-checkbox:checked:not(:disabled)').length > 0;
    $('#btnEnviarExtension').prop('disabled', !hayProductosSeleccionados);
}

// Función para enviar los datos al backend - CON DEBUG COMPLETO
// Función para enviar los datos al backend - CON DEBUG EXTREMO
function enviar_Solicitud_Extension() {
    console.log('🎯🎯🎯 EJECUTANDO enviarSolicitudExtension 🎯🎯🎯');
    
    // 1. VERIFICAR SI LOS CHECKBOXES EXISTEN
    var checkboxes = $('.producto-checkbox');
    console.log('🔍 CHECKBOXES ENCONTRADOS:', checkboxes.length);
    
    // 2. Obtener tokens seleccionados
    var selectedTokens = [];
    $('.producto-checkbox:checked:not(:disabled)').each(function() {
        console.log('✅ CHECKBOX SELECCIONADO:', $(this).val());
        selectedTokens.push($(this).val());
    });
    
    console.log('🔍 TOKENS SELECCIONADOS:', selectedTokens);
    console.log('🔍 CANTIDAD TOKENS:', selectedTokens.length);
    
    // 3. MOSTRAR TODOS LOS CHECKBOXES Y SUS VALORES
    console.log('🔍 INSPECCIÓN COMPLETA DE CHECKBOXES:');
    $('.producto-checkbox').each(function(index) {
        var checkbox = $(this);
        console.log(`Checkbox ${index}:`, {
            id: checkbox.attr('id'),
            value: checkbox.val(),
            checked: checkbox.is(':checked'),
            disabled: checkbox.is(':disabled'),
            clase: checkbox.attr('class'),
            html_completo: checkbox.parent().html()
        });
    });
    
    if (selectedTokens.length === 0) {
        console.log('❌ NO HAY TOKENS SELECCIONADOS - MOSTRANDO ALERTA');
        Swal.fire('Error', 'Por favor seleccione al menos un producto para extensión', 'error');
        return;
    }
    
    var tipoExtension = $('#tipoExtension').val();
    var nuevaFecha = $('#nuevaFecha').val();
    var motivo = $('#motivo').val();
    
    console.log('📝 DATOS DEL FORMULARIO:');
    console.log('📝 Tipo Extensión:', tipoExtension);
    console.log('📝 Nueva Fecha:', nuevaFecha);
    console.log('📝 Motivo:', motivo);
    
    if (!nuevaFecha || !motivo) {
        console.log('❌ FALTAN CAMPOS DEL FORMULARIO');
        Swal.fire('Error', 'Por favor complete todos los campos', 'error');
        return;
    }
    
    $('#btnEnviarExtension').prop('disabled', true);
    $('#btnEnviarExtension').html('<span class="spinner-border spinner-border-sm" role="status"></span> Enviando...');
    
    // 4. Construir URL EXACTA
    var url = 'components/socios/models/solicitar_extension.php?' +
              'tipo_extension=' + encodeURIComponent(tipoExtension) +
              '&nueva_fecha=' + encodeURIComponent(nuevaFecha) +
              '&motivo=' + encodeURIComponent(motivo);
    
    // Agregar tokens
    selectedTokens.forEach(function(token) {
        url += '&tokens[]=' + encodeURIComponent(token);
    });
    
    console.log('📤 URL COMPLETA:');
    console.log(url);
    console.log('🔍 URL contiene tokens[]?:', url.includes('tokens[]'));
    
    // 5. Hacer la petición AJAX
    console.log('🔄 INICIANDO PETICIÓN AJAX...');
    
    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('✅ RESPUESTA EXITOSA:', response);
            $('#btnEnviarExtension').prop('disabled', false);
            $('#btnEnviarExtension').html('Solicitar Extensión');
            
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Solicitud Enviada',
                    html: response.mensaje,
                    confirmButtonText: 'Aceptar'
                }).then((result) => {
                    $('#modalExtension').modal('hide');
                    
                    // 🔥 ACTUALIZAR SOLO LA TABLA
                    //actualizarTablaPrestamos();
                    location.reload();
                    
                    // Limpiar el formulario
                    $('#nuevaFecha').val('');
                    $('#motivo').val('');
                    $('.producto-checkbox').prop('checked', false);
                    $('#selectAll').prop('checked', false);
                });
            } else {
                Swal.fire('Error', response.mensaje, 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ ERROR EN AJAX:');
            console.error('Status:', status);
            console.error('Error:', error);
            console.error('Response:', xhr.responseText);
            
            $('#btnEnviarExtension').prop('disabled', false);
            $('#btnEnviarExtension').html('Solicitar Extensión');
            Swal.fire('Error', 'Error al enviar la solicitud: ' + error, 'error');
        }
    });
}


// Función para actualizar la tabla de préstamos
function actualizarTablaPrestamos() {
    console.log('🔄 Actualizando tabla de préstamos...');
    
    $.ajax({
        url: 'components/socios/models/obtener_tabla_prestamos.php',
        type: 'POST',
        data: { 
            action: 'obtener_tabla_prestamos'
            // El ID de usuario se obtiene automáticamente de la sesión
        },
        success: function(response) {
            console.log('✅ Tabla actualizada correctamente');
            // Reemplazar solo el cuerpo de la tabla
            $('#tablaprestamos tbody').html(response);
            
            // Opcional: Re-inicializar DataTables si los usas
            if ($.fn.DataTable.isDataTable('#tablaprestamos')) {
                $('#tablaprestamos').DataTable().destroy();
                // Re-inicializar DataTables aquí si es necesario
                // $('#tablaprestamos').DataTable({ ... });
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Error actualizando tabla:', error);
        }
    });
}




// 🔥 VERIFICAR QUE EL BOTÓN ESTÁ CONECTADO CORRECTAMENTE
/*
$(document).ready(function() {
    console.log('✅ DOCUMENTO LISTO - CONFIGURANDO BOTÓN');
    
    // Verificar que el botón existe
    var btn = $('#btnEnviarExtension');
    console.log('🔍 BOTÓN ENCONTRADO:', btn.length);
    
    if (btn.length === 0) {
        console.error('❌ BOTÓN NO ENCONTRADO EN EL DOM');
        return;
    }
    
    console.log('🔍 HTML DEL BOTÓN:', btn.html());
    
    // Forzar el evento click
    btn.off('click').on('click', function(e) {
        console.log('🎯 CLICK EN BOTÓN DETECTADO - EJECUTANDO FUNCIÓN');
        e.preventDefault();
        enviarSolicitudExtension();
    });
    
    console.log('✅ BOTÓN CONFIGURADO CORRECTAMENTE');
});

// 🔥 AGREGAR ESTO PARA VERIFICAR QUE LA FUNCIÓN ES GLOBAL
console.log('🔍 VERIFICANDO FUNCIÓN GLOBAL:', typeof window.enviarSolicitudExtension);


// 🔥 AGREGAR ESTO PARA VERIFICAR QUE EL BOTÓN ESTÁ CONECTADO
$(document).ready(function() {
    console.log('✅ DOCUMENTO LISTO - VERIFICANDO BOTÓN');
    
    // Verificar que el botón existe y tiene el evento
    var btn = $('#btnEnviarExtension');
    console.log('🔍 BOTÓN ENCONTRADO:', btn.length > 0);
    console.log('🔍 HTML DEL BOTÓN:', btn.html());
    
    // Forzar el evento onclick por si acaso
    btn.off('click').on('click', function() {
        console.log('🎯 CLICK EN BOTÓN DETECTADO');
        enviarSolicitudExtension();
    });
    
    console.log('✅ CONFIGURACIÓN COMPLETADA');
});
*/



// === EVENT LISTENERS ACTUALIZADOS ===
$(document).ready(function() {
    console.log('✅ Documento listo - Configurando event listeners');
    
    // 🔥 BUSCAR BOTONES CON LOS NUEVOS NOMBRES
    var botonesPrimera = $('button[onclick*="sol_ext1("]').length;
    var botonesSegunda = $('button[onclick*="sol_ext2("]').length;
    console.log(`🔍 Botones encontrados: ${botonesPrimera} primera extensión, ${botonesSegunda} segunda extensión`);
    
    // Event listener para todos los botones - ACTUALIZADO
    $(document).on('click', 'button', function() {
        var onclick = $(this).attr('onclick') || '';
        
        if (onclick.includes('sol_ext1(')) {
            var match = onclick.match(/sol_ext1\("([^"]+)"\)/);
            if (match && match[1]) {
                console.log('🟢 Ejecutando primera extensión (sol_ext1)');
                window.sol_ext1(match[1]);
                return false;
            }
        }
        else if (onclick.includes('sol_ext2(')) {
            var match = onclick.match(/sol_ext2\("([^"]+)"\)/);
            if (match && match[1]) {
                console.log('🟢 Ejecutando segunda extensión (sol_ext2)');
                window.sol_ext2(match[1]);
                return false;
            }
        }
        
        // 🔥 MANTENER COMPATIBILIDAD CON NOMBRES ANTIGUOS TEMPORALMENTE
        /*else if (onclick.includes('solicitarextension(')) {
            var match = onclick.match(/solicitarextension\("([^"]+)"\)/);
            if (match && match[1]) {
                console.log('🟡 Ejecutando primera extensión (nombre antiguo)');
                window.sol_ext1(match[1]);
                return false;
            }
        }*/
        /*else if (onclick.includes('solicitarextension2(')) {
            var match = onclick.match(/solicitarextension2\("([^"]+)"\)/);
            if (match && match[1]) {
                console.log('🟡 Ejecutando segunda extensión (nombre antiguo)');
                window.sol_ext2(match[1]);
                return false;
            }
        }*/
    });
    
    console.log('✅ Configuración completada');
});

console.log('✅ Script de extensiones cargado - Nombres actualizados');

</script>