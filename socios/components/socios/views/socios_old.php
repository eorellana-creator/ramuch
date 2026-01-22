<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<div class="card">
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

                <!-- Historial de Préstamos Tab -->
                <div class="tab-pane fade" id="nav-prestamo" role="tabpanel" aria-labelledby="nav-prestamo-tab">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <i class="fa fa-align-justify"></i> Historial de Préstamos <a href="index.php?component=equipo&view=equipo_list"><button type="button" class="btn btn-primary">Pedir equipo-></button></a>
                                        <!-- Botón para ver listado de atrasos -->
                                        <button type="button" class="btn btn-warning" style="margin-left: 10px;" onclick="cargarAtrasos()">
                                            Ver listado de atrasos
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <table id="tablaprestamos" class="display" style="width:100%">
                                            <thead>
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
                                            <?php echo $datos_prestamo;?>
                                        </table>
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
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-warning" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Rut ya existe</h4>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
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

<div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModal2Label" aria-hidden="true">
    <div class="modal-dialog modal-warning" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Correo ya existe</h4>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
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

<!-- Modal para solicitar extensión -->

<div class="modal fade" id="modalExtension" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Solicitar Extensión</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
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
                    <input type="hidden" id="tokenPrestamo" name="tokenPrestamo">
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
                <button type="button" id="btnEnviarExtension" class="btn btn-primary" onclick="enviarSolicitudExtension()">
                    Solicitar Extensión
                </button>
            </div>
        </div>
    </div>
</div>

<!-- NUEVO MODAL PARA ATRASOS -->
<div class="modal fade" id="modalAtrasos" tabindex="-1" role="dialog" aria-labelledby="modalAtrasosLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="modalAtrasosLabel">Listado de Préstamos con Atraso</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
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


<script>
// Función para cargar los atrasos
function cargarAtrasos() {
    // Mostrar el modal
    $('#modalAtrasos').modal('show');
    
    // Mostrar loading y ocultar otros contenidos
    $('#loadingAtrasos').show();
    $('#contenidoAtrasos').hide();
    $('#sinAtrasos').hide();
    
    // Realizar la petición al backend
    $.ajax({
        url: 'components/socios/models/atrasos.php',
        type: 'POST',
        data: {
            action: 'obtenerAtrasos'
            // Ya no enviamos socio_id porque el backend lo obtiene de la sesión
        },
        dataType: 'json',
        success: function(response) {
            console.log("Respuesta del servidor:", response);
            $('#loadingAtrasos').hide();
            
            if (response.success) {
                if (response.data && response.data.length > 0) {
                    // Limpiar tabla
                    $('#cuerpoTablaAtrasos').empty();
                    
                    // Llenar tabla con los datos
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
                            '</td>' +
                            '</tr>';

                        $('#cuerpoTablaAtrasos').append(fila);
                    });
                    
                    $('#contenidoAtrasos').show();
                } else {
                    $('#sinAtrasos').show();
                }
            } else {
                // Mostrar error
                Swal.fire('Error', response.message || 'Error al cargar los atrasos', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error("Error en la petición AJAX:");
            console.error("Status:", status);
            console.error("Error:", error);
            console.error("ResponseText:", xhr.responseText);
            
            $('#loadingAtrasos').hide();
            
            // Intentar parsear la respuesta aunque haya error
            try {
                var response = JSON.parse(xhr.responseText);
                Swal.fire('Error', response.message || 'Error en el servidor: ' + error, 'error');
            } catch (e) {
                Swal.fire('Error', 'Error de conexión al servidor: ' + error, 'error');
            }
        }
    });
}
</script>
