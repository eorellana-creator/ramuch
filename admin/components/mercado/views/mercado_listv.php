<?php echo $mensaje;?>

<!-- para mostrar los mensajes 
echo '<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">';
echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
-->
<?php
// Código PHP aquí
?>
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<div class="card">
    <div class="card-header">
        <i class="fas fa-store"></i> <strong>Mercado Ramuch</strong>&nbsp; &nbsp; 
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalProducto">
            <i class="fas fa-plus" aria-hidden="true"></i> Agregar nuevo producto
        </button>
        <button type="button" class="btn btn-info ml-2" data-toggle="modal" data-target="#ventasModal">
            <i class="fas fa-chart-line"></i> Ventas
        </button>
        <button type="button" class="btn btn-success ml-2" data-toggle="modal" data-target="#carritoModal">
            <i class="fas fa-shopping-cart"></i> Carrito <span id="cartCount" class="badge badge-light">0</span>
        </button>
        <a href="javascript:document.location.reload();">
            <span class="badge badge-primary float-right" style='padding:6px;margin-bottom:6px;'>
                <i class="fas fa-sync"></i> Recargar datos y borra carrito
            </span>
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="tabla" class="table table-striped table-hover dt-responsive display" style="width:100%;">
                <thead>
                    <tr>
                        <th>Agregar</th>
                        <th>N°</th>
                        <th>Imagen</th>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Categoría</th>
                        <th>Marca</th>
                        <th>Estado</th>
                        <th>Editar</th>
                        <th>Eliminar</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Modal de Producto -->
<div class="modal fade" id="modalProducto" tabindex="-1" role="dialog" aria-labelledby="modalProductoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalProductoLabel">Nuevo Producto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formProducto" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombre">Nombre del Producto *</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>
                            <div class="form-group">
                                <label for="precio">Precio *</label>
                                <input type="text" class="form-control numeric-input" id="precio" name="precio" required>
                            </div>
                            <div class="form-group">
                                <label for="stock">Stock *</label>
                                <input type="number" class="form-control" id="stock" name="stock" required min="0">
                            </div>
                            <div class="form-group">
                                <label for="stock_minimo">Stock Mínimo</label>
                                <input type="number" class="form-control" id="stock_minimo" name="stock_minimo" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="categoria_id">Categoría *</label>
                                <select class="form-control" id="categoria_id" name="categoria_id" required>
                                    <!-- Se carga dinámicamente -->
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="marca">Marca</label>
                                <input type="text" class="form-control" id="marca" name="marca">
                            </div>
                            <div class="form-group">
                                <label for="modelo">Modelo</label>
                                <input type="text" class="form-control" id="modelo" name="modelo">
                            </div>
                            <div class="form-group">
                                <label for="estado">Estado *</label>
                                <select class="form-control" id="estado" name="estado" required>
                                    <option value="Activo">Activo</option>
                                    <option value="Inactivo">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="talla">Talla</label>
                                <input type="text" class="form-control" id="talla" name="talla">
                            </div>
                            <div class="form-group">
                                <label for="color">Color</label>
                                <input type="text" class="form-control" id="color" name="color">
                            </div>
                            <div class="form-group">
                                <label for="descuento">Descuento (%)</label>
                                <input type="number" class="form-control" id="descuento" name="descuento" min="0" max="100" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="imagen">Imagen del Producto</label>
                                <input type="file" class="form-control-file" id="imagen" name="imagen" accept="image/*">
                                <div id="imagePreview" class="mt-2"></div>
                            </div>
                            <div class="form-group">
                                <label for="descripcion">Descripción</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Producto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Detalles -->
<div class="modal fade" id="detalleModal" tabindex="-1" role="dialog" aria-labelledby="detalleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detalleModalLabel">Detalles del Producto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="detalleModalBody">
                <!-- El contenido se cargará dinámicamente -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal del Carrito -->
<div class="modal fade" id="carritoModal" tabindex="-1" role="dialog" aria-labelledby="carritoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="carritoModalLabel">Carrito de Compras</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="carritoContent">
                    <!-- El contenido se cargará dinámicamente -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <!--<button type="button" class="btn btn-success" id="btnProcesarCompra">Procesar Compra</button>  -->
                <button type="button" class="btn btn-success" id="btnProcesarCompra" onclick="procesarPago()">Procesar Compra</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Imagen -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Imagen del Producto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <div class="image-container" style="overflow: auto;">
                    <img id="modalImage" src="" alt="Imagen Ampliada" style="transform: scale(1.2); transform-origin: center center;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Edición de Producto -->
<div class="modal fade" id="modalEditProducto" tabindex="-1" role="dialog" aria-labelledby="modalEditProductoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditProductoLabel">Editar Producto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formEditProducto" enctype="multipart/form-data">
                    <input type="hidden" id="edit_product_id" name="edit_product_id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_nombre">Nombre del Producto *</label>
                                <input type="text" class="form-control" id="edit_nombre" name="edit_nombre" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_precio">Precio *</label>
                                <input type="text" class="form-control numeric-input" id="edit_precio" name="edit_precio" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_stock">Stock *</label>
                                <input type="number" class="form-control" id="edit_stock" name="edit_stock" required min="0">
                            </div>
                            <div class="form-group">
                                <label for="edit_stock_minimo">Stock Mínimo</label>
                                <input type="number" class="form-control" id="edit_stock_minimo" name="edit_stock_minimo" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_categoria_id">Categoría *</label>
                                <select class="form-control" id="edit_categoria_id" name="edit_categoria_id" required>
                                    <!-- Se carga dinámicamente -->
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="edit_marca">Marca</label>
                                <input type="text" class="form-control" id="edit_marca" name="edit_marca">
                            </div>
                            <div class="form-group">
                                <label for="edit_modelo">Modelo</label>
                                <input type="text" class="form-control" id="edit_modelo" name="edit_modelo">
                            </div>
                            <div class="form-group">
                                <label for="edit_estado">Estado *</label>
                                <select class="form-control" id="edit_estado" name="edit_estado" required>
                                    <option value="Activo">Activo</option>
                                    <option value="Inactivo">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_talla">Talla</label>
                                <input type="text" class="form-control" id="edit_talla" name="edit_talla">
                            </div>
                            <div class="form-group">
                                <label for="edit_color">Color</label>
                                <input type="text" class="form-control" id="edit_color" name="edit_color">
                            </div>
                            <div class="form-group">
                                <label for="edit_descuento">Descuento (%)</label>
                                <input type="number" class="form-control" id="edit_descuento" name="edit_descuento" min="0" max="100" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_imagen">Imagen del Producto</label>
                                <input type="file" class="form-control-file" id="edit_imagen" name="edit_imagen" accept="image/*">
                                <div id="currentImagePreview" class="mt-2"></div>
                            </div>
                            <div class="form-group">
                                <label for="edit_descripcion">Descripción</label>
                                <textarea class="form-control" id="edit_descripcion" name="edit_descripcion" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Ventas -->
<div class="modal fade" id="ventasModal" tabindex="-1" role="dialog" aria-labelledby="ventasModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ventasModalLabel">Listado de Ventas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table id="tablaVentas" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID Venta</th>
                            <th>Fecha de la Venta</th>
                            <th>Comprador</th>
                            <th>Total Pagado</th>
                            <th>Orden flow</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Las filas se llenarán dinámicamente -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Devolución -->
<div class="modal fade" id="devolucionModal" tabindex="-1" role="dialog" aria-labelledby="devolucionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="devolucionModalLabel">Devolución de Venta</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table id="tablaDetalleVenta" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Unitario</th>
                            <th>Total</th>
                            <th>Devolver</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Las filas se llenarán dinámicamente -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnConfirmarDevolucion">Confirmar Devolución</button>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function() {
    $('#ventasModal').on('show.bs.modal', function (e) {
        $.ajax({
            url: 'components/mercado/views/obtener_ventas.php',
            method: 'GET',
            success: function(data) {
                console.log(data); // Depuración: Verifica la respuesta
                var tbody = $('#tablaVentas tbody');
                tbody.empty();

                if (data.length > 0) {
                    data.forEach(function(venta) {
                        var row = `<tr>
                            <td>${venta.id}</td>
                            <td>${venta.fecha}</td>
                            <td>${venta.comprador}</td>
                            <td>${venta.total}</td>
                            <td>${venta.orden_flow}</td>
                            <td>
                                <button class="btn btn-warning btn-sm" onclick="devolucion(${venta.id})">Devolución</button>
                            </td>
                        </tr>`;
                        //<button class="btn btn-info btn-sm" onclick="cambio(${venta.id})">Cambio</button>
                        tbody.append(row);
                    });
                } else {
                    tbody.append('<tr><td colspan="6" class="text-center">No hay ventas registradas.</td></tr>');
                }
            },
            error: function(xhr, status, error) {
                console.error("Error en la solicitud AJAX:", status, error); // Depuración: Verifica errores
                alert('Error al cargar las ventas');
            }
        });
    });
});

function devolucion(idVenta) {
    console.log("ID de Venta recibido en devolucion():", idVenta); // Depuración: Verifica el ID de la venta

    // Limpiar la tabla de detalles
    $('#tablaDetalleVenta tbody').empty();

    // Obtener los detalles de la venta
    $.ajax({
        url: 'components/mercado/views/obtener_detalle_venta.php', // Archivo PHP para obtener los detalles
        method: 'GET',
        data: { id_venta: idVenta }, // Enviar el ID de la venta
        success: function(data) {
            console.log("Respuesta del servidor:", data); // Depuración: Verifica la respuesta

            if (Array.isArray(data)) {
                // Llenar la tabla con los detalles de la venta
                data.forEach(function(item) {
                    var row = `<tr>
                        <td>${item.nombre_producto}</td>
                        <td>${item.cantidad}</td>
                        <td>${item.precio_unitario}</td>
                        <td>${item.total}</td>
                        <td>
                            <input type="checkbox" class="devolver-item" data-producto-id="${item.id_producto}" data-cantidad="${item.cantidad}" data-id-venta="${idVenta}">
                        </td>
                    </tr>`;
                    $('#tablaDetalleVenta tbody').append(row);
                });
            } else {
                console.error("La respuesta no es un array:", data);
                alert('Error: La respuesta del servidor no es válida.');
            }
            // Mostrar el modal de devolución
            $('#devolucionModal').modal('show');
        },
        error: function(xhr, status, error) {
            console.error("Error en la solicitud AJAX:", status, error); // Depuración: Verifica errores
            alert('Error al cargar los detalles de la venta');
        }
    });
}

$(document).ready(function() {
    // Botón para confirmar la devolución
    $('#btnConfirmarDevolucion').on('click', function() {
    var itemsDevolver = [];

    // Recorrer los ítems seleccionados
    $('.devolver-item:checked').each(function() {
        var productoId = $(this).data('producto-id'); // Obtener el ID del producto
        var cantidad = $(this).data('cantidad'); // Obtener la cantidad
        var idVenta = $(this).data('id-venta'); // Obtener el ID de la venta

        console.log("Ítems a devolver productoId:", productoId); 
        console.log("Ítems a devolver cantidad:", cantidad); 
        console.log("Ítems a devolver idVenta:", idVenta); 

        // Verificar que los valores no sean undefined
        if (productoId !== undefined && cantidad !== undefined && idVenta !== undefined) {
            itemsDevolver.push({
                id_producto: productoId,
                cantidad: cantidad,
                id_venta: idVenta
            });
        } else {
            console.error("Datos incompletos en el ítem:", $(this));
        }
    });

    console.log("Ítems a devolver:", itemsDevolver); // Depuración: Verificar los ítems seleccionados

    if (itemsDevolver.length > 0) {
        
        // Enviar los ítems a devolver al servidor como JSON
        $.ajax({
            url: 'components/mercado/views/procesar_devolucion.php', // Archivo PHP para procesar la devolución
            method: 'POST',
            contentType: 'application/json', // Especificar que se envía JSON
            data: JSON.stringify({ items: itemsDevolver }), // Convertir a cadena JSON
            success: function(response) {
                console.log("itemsDevolver en funcion boton devolucion:", itemsDevolver);
                alert('Devolución procesada correctamente');
                $('#devolucionModal').modal('hide');
                // Recargar la página o actualizar la tabla de ventas
                //console.log("itemsDevolver en funcion boton devolucion:", itemsDevolver);
                //console.log("Ítems a devolver idVenta:", idVenta);
                location.reload();
            },
            error: function(xhr, status, error) {
                console.error("Error en la solicitud AJAX:", status, error); // Depuración: Verifica errores
                alert('Error al procesar la devolución');
            }
        });
    } else {
        alert('Selecciona al menos un ítem para devolver.');
    }
    });
});

function cambio(idVenta) {
    // Lógica para manejar el cambio
    alert('Cambio de la venta ' + idVenta);
}
</script>

<!-- Scripts -->
<script type="module" src="components/mercado/js/main.js"></script>
<script type="module" src="components/mercado/js/cartManager.js"></script>
<script type="module" src="components/mercado/js/productManager.js"></script>