<?php echo $mensaje; ?>

<div class="card">
    <div class="card-header">
        <i class="fas fa-hiking"></i> <strong>Equipos</strong>&nbsp; &nbsp; 
        <a href="components/equipo/excel/lista_equipo<?php echo $_SESSION['usuario_id'];?>.xls" download target="_blank">
            <i class="fas fa-file-excel"></i> Descargar en Excel
        </a>&nbsp; &nbsp; 
        
        <a href="index.php?component=equipo&amp;view=equipo">
            <button type="button" class="btn btn-primary">
                <i class="fas fa-plus" aria-hidden="true"></i> Agregar nuevo Equipo
            </button>
        </a>

        <?php
        // Consulta estado actual del préstamo
        include("../../../configuration.php");
        include("../../../includes/conexionMysql.php");
        include("../../../includes/funciones.php");
        
        $sql = $mysql->query("SELECT valor FROM valores WHERE id = 1");
        $prestamo_activo = 0;
        if ($mysql->f_num($sql) > 0) {
            $row = $mysql->f_array($sql);
            $prestamo_activo = $row['valor'];
        }
        ?>

        <button type="button" class="btn btn-<?php echo ($prestamo_activo == 1) ? 'success' : 'danger'; ?>" 
                id="togglePrestamo" style="margin-left: 10px;">
            <i class="fas fa-power-off"></i> Préstamo <?php echo ($prestamo_activo == 1) ? 'Activo' : 'Desactivado'; ?>
        </button>

        <a href="javascript:document.location.reload();">
            <span class="badge badge-primary float-right" style='padding:6px;margin-bottom:6px;'>
                <i class="fas fa-sync"></i> Recargar datos
            </span>
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="tabla" class="table table-striped table-hover dt-responsive display" style="width:100%;">
                <thead>
                    <tr>
                        <th>N°</th>
                        <th>Imagen</th>
                        <th>Nombre de Equipo</th>
                        <th>ID Único</th>
                        <th>Estado</th>
                        <th style="min-width:130px;">Prestamo a</th>
                        <th>Acciones</th>
                        <th>Responsable Préstamo</th>
                        <th>Ver Detalle</th>
                        <th>Editar Equipo</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Modal de Devolución -->
<div class="modal fade" id="primaryModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-primary" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Devolución de Equipo</h4>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <label>Observación:</label>
                <input type="text" class="form-control" id="observacion" placeholder="Observación de la devolución (obligatorio)" maxlength="255" required>
                <div class="invalid-feedback">Por favor ingresa una observación</div>
                
                <label class="mt-3">Estado de la devolución:</label>
                <select id="estado" class="form-control">
                    <option value="En el mismo estado">En el mismo estado</option>
                    <option value="Con detalles">Con detalles</option>
                    <option value="Extraviado">Extraviado</option>
                    <option value="Inutilizable">Inutilizable</option>
                </select>

                <!-- Checkbox Devolver Todo -->
                <div class="alert alert-warning mt-3 mb-0">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="devolverTodo">
                        <label class="form-check-label" for="devolverTodo">
                            Devolver todo de <span id="nombreUsuario"></span>
                        </label>
                    </div>
                </div>

                <!-- Listado de equipos -->
                <div id="equiposList" class="mt-3" style="max-height: 300px; overflow-y: auto;"></div>

                <!-- Campos ocultos -->
                <input type="hidden" id="idUsuario">
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button class="btn btn-primary" id="btnDevolver" onclick="devolverEquipo()">Devolver</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Detalles -->
<div class="modal fade" id="detalleModal" tabindex="-1" role="dialog" aria-labelledby="detalleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detalleModalLabel">Detalles del Equipo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="detalleModalBody">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Imagen -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Imagen del Equipo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="Imagen Ampliada" class="img-fluid">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal para rechazar solicitud -->
<div class="modal fade" id="modalRechazo" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Rechazar Solicitud</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="tokenRechazo">
                <div class="form-group">
                    <label>Motivo del rechazo:</label>
                    <textarea id="motivoRechazo" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" onclick="confirmarRechazo()">Confirmar Rechazo</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para gestionar extensión - Versión mejorada -->
<div class="modal fade" id="modalExtension" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Gestionar Extensión</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="tokenExtension">
                <input type="hidden" id="fechaPropuestaOriginal">
                
                <div id="debugInfo"></div>
                <div class="small text-muted mb-3">Token: <span id="tokenDebug" class="font-weight-bold"></span></div>

                <!-- Selector de acción -->
                <div class="form-group">
                    <label>Acción:</label>
                    <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                        <label class="btn btn-outline-success active">
                            <input type="radio" name="accionExtension" value="aprobar" checked> Aprobar
                        </label>
                        <label class="btn btn-outline-danger">
                            <input type="radio" name="accionExtension" value="rechazar"> Rechazar
                        </label>
                    </div>
                </div>
                
                <!-- Campo de motivo -->
                <div class="form-group">
                    <label id="labelMotivo">Motivo (opcional para aprobación, requerido para rechazo):</label>
                    <textarea id="motivoExtension" class="form-control" rows="3"></textarea>
                </div>
                
                <!-- Nueva fecha para aprobación -->
                <div class="form-group" id="fechaExtensionGroup">
                    <label>Fecha de devolución propuesta:</label>
                    <div class="input-group mb-2">
                        <input type="date" id="fechaExtension" class="form-control">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" onclick="restaurarFechaOriginal()">
                                <i class="fas fa-undo"></i> Restaurar
                            </button>
                        </div>
                    </div>
                    <small class="text-muted">Fecha original propuesta: <span id="fechaOriginalText"></span></small>
                </div>
                
                <!-- Opción para aplicar a todos -->
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="aplicarATodos">
                    <label class="form-check-label" for="aplicarATodos">Aplicar a todos los equipos del usuario</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="confirmarGestionExtension()">Confirmar</button>
            </div>
        </div>
    </div>
</div>


<script>
// Almacenar token global
let currentToken = '';

// Configurar modal al abrir
document.querySelectorAll('.btn-gestion-extension').forEach(btn => {
  btn.addEventListener('click', function() {
    currentToken = this.getAttribute('data-token');
    document.getElementById('motivoContainer').style.display = 'none';
    document.getElementById('motivoExtension').value = '';
  });
});

// Mostrar campo para motivo al rechazar
function mostrarMotivo() {
  document.getElementById('motivoContainer').style.display = 'block';
}

// Confirmar acción
function confirmarAccion(accion) {
  const motivo = accion === 'rechazar' ? document.getElementById('motivoExtension').value : '';
  
  if (accion === 'rechazar' && !motivo) {
    alert('Ingrese el motivo del rechazo');
    return;
  }

  fetch('procesar_solicitud.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: `token=${currentToken}&accion=${accion}&motivo=${encodeURIComponent(motivo)}`
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      alert(data.message);
      bootstrap.Modal.getInstance('#modalDecisionExtension').hide();
      location.reload();
    } else {
      alert('Error: ' + data.error);
    }
  });
}
</script>


<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// Función para alternar estado de préstamo
$('#togglePrestamo').click(function() {
    var boton = $(this);
    $.ajax({
        url: 'components/equipo/models/toggle_prestamo.php',
        type: 'POST',
        dataType: 'json',
        beforeSend: function() {
            boton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Procesando...');
        },
        success: function(response) {
            if(response.success) {
                // Actualizar el botón
                boton.prop('disabled', false)
                    .removeClass(response.nuevo_estado == 1 ? 'btn-danger' : 'btn-success')
                    .addClass(response.nuevo_estado == 1 ? 'btn-success' : 'btn-danger')
                    .html('<i class="fas fa-power-off"></i> Préstamo ' + (response.nuevo_estado == 1 ? 'Activo' : 'Desactivado'));
                
                // Mostrar notificación
                mostrarNotificacion('success', 'Estado actualizado', 'El préstamo de equipos ha sido ' + 
                                  (response.nuevo_estado == 1 ? 'activado' : 'desactivado'));
            } else {
                mostrarNotificacion('error', 'Error', response.message || 'Error al actualizar el estado');
                boton.prop('disabled', false).html('<i class="fas fa-power-off"></i> Error');
            }
        },
        error: function() {
            mostrarNotificacion('error', 'Error', 'No se pudo conectar con el servidor');
            boton.prop('disabled', false).html('<i class="fas fa-power-off"></i> Error');
        }
    });
});

// Función para mostrar notificaciones
function mostrarNotificacion(tipo, titulo, mensaje) {
    // Puedes implementar tu propio sistema de notificaciones o usar toastr/sweetalert
    alert(titulo + ': ' + mensaje);
}

document.addEventListener("DOMContentLoaded", function() {
    // Configuración del modal de imagen
    $('#imageModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var imageUrl = button.data('img');
        var modal = $(this);
        modal.find('#modalImage').attr('src', imageUrl);
    });

    // Configuración del modal de detalles
    $('#detalleModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var token = button.data('token');
        var modal = $(this);
        
        // Cargar detalles vía AJAX
        $.ajax({
            url: 'components/equipo/models/get_equipo_details.php',
            type: 'GET',
            data: { token: token },
            success: function(response) {
                modal.find('#detalleModalBody').html(response);
            },
            error: function() {
                modal.find('#detalleModalBody').html('Error al cargar los detalles del equipo.');
            }
        });
    });
});

// Carga de equipos en devolución
$(document).on('click', '.devolucion-btn', function() {
    var idUsuario = $(this).data('id');
    var nombreUsuario = $(this).data('nombre');
    
    $.ajax({
        url: 'components/equipo/models/get_equipos_usuario.php',
        data: { id_usuario: idUsuario },
        success: function(response) {
            var equipos = JSON.parse(response);
            var html = '<div class="list-group">';
            
            equipos.forEach(function(equipo) {
                html += `
                    <label class="list-group-item d-flex justify-content-between">
                        <div>
                            <input class="form-check-input me-2 equipo-check" 
                            type="checkbox" 
                            value="${equipo.id_equipo_prestamo}"
                            checked>
                            <span>
                                ${equipo.id_equipo} - ${equipo.nombre}
                            </span>
                        </div>
                    </label>
                `;
            });            

            html += '</div>';
            $('#equiposList').html(html);
            
            $('#devolverTodo').prop('checked', true);
            $('#nombreUsuario').text(nombreUsuario);
            $('#idUsuario').val(idUsuario);
        }
    });
});

// Controlar selección masiva
$(document).on('change', '#devolverTodo', function() {
    $('.equipo-check').prop('checked', this.checked);
});

// Controlar cambios individuales
$(document).on('change', '.equipo-check', function() {
    const todosMarcados = $('.equipo-check').length === $('.equipo-check:checked').length;
    $('#devolverTodo').prop('checked', todosMarcados);
});

function devolverEquipo() {
    // Obtener equipos seleccionados
    const equipos = $('.equipo-check:checked').map((i, el) => el.value).get();
    
    // Verificar selección
    if(equipos.length === 0) {
        alert("Seleccione al menos un equipo para devolver");
        return;
    }

    // Obtener otros datos del formulario
    const observacion = $('#observacion').val();
    const estado = $('#estado').val();
    const idUsuario = $('#idUsuario').val();

    // Enviar solicitud AJAX
    $.post('components/equipo/models/devolver_equipo.php', {
        equipos: JSON.stringify(equipos),
        observacion: observacion,
        estado: estado,
        idUsuario: idUsuario
    })
    .done(response => {
        if(response.success) {
            location.reload();
        } else {
            location.reload();
        }
    })
    .fail((jqXHR, textStatus, errorThrown) => {
        console.error("Error en la solicitud:", textStatus, errorThrown);
        alert("Error en la comunicación con el servidor");
    });
}

// Función para verificar checkboxes y habilitar/deshabilitar botón
function verificarCheckboxes() {
    const tieneChecks = $('.equipo-check').length > 0;
    const checksMarcados = $('.equipo-check:checked').length > 0;
    $('#btnDevolver').prop('disabled', !tieneChecks || !checksMarcados);
}

// Evento para verificar checkboxes cuando se muestra el modal
$('#primaryModal').on('show.bs.modal', function() {
    verificarCheckboxes();
});

// Evento para verificar checkboxes cuando cambian
$(document).on('change', '.equipo-check, #devolverTodo', function() {
    verificarCheckboxes();
});



////////////////////////////////////////////////////////////////////////
document.addEventListener('DOMContentLoaded', function() {
    const decisionModal = new bootstrap.Modal('#modalDecisionExtension');
    let currentToken;
    
    // Cuando se hace clic en el botón de gestión
    document.querySelectorAll('.btn-gestion-extension').forEach(btn => {
        btn.addEventListener('click', function() {
            currentToken = this.getAttribute('data-token');
            document.getElementById('modalTokenInfo').textContent = 'Token: ' + currentToken;
        });
    });
    
    // Botón Aprobar
    document.getElementById('aprobarExtension').addEventListener('click', function() {
        console.log('Aprobando extensión para:', currentToken);
        // Aquí llamarías a: gestionarExtension(currentToken, 'aprobar');
        decisionModal.hide();
        
        // Mostrar feedback al usuario
        alert('Extensión aprobada correctamente');
    });
    
    // Botón Rechazar
    document.getElementById('rechazarExtension').addEventListener('click', function() {
        console.log('Rechazando extensión para:', currentToken);
        // Aquí llamarías a: gestionarExtension(currentToken, 'rechazar');
        decisionModal.hide();
        
        // Mostrar feedback al usuario
        alert('Extensión rechazada');
    });
});


document.getElementById('confirmarAccion').addEventListener('click', function() {
    const accion = this.dataset.accion; // 'aprobar' o 'rechazar'
    const token = document.getElementById('modalToken').value;
    const motivo = document.getElementById('motivoExtension').value;
    
    fetch('components/equipo/models/procesar_solicitud.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `token=${token}&accion=${accion}&motivo=${motivo}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload(); // Recargar para ver cambios
        } else {
            alert('Error: ' + data.error);
        }
    });
});

</script>