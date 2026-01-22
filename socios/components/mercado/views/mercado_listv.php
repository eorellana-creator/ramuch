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



<div class="card">
    <div class="card-header">
        <i class="fas fa-store"></i> <strong>Mercado Ramuch</strong>&nbsp; &nbsp; 
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
                        <th>editar</th>
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

<!-- Scripts -->
<script type="module" src="components/mercado/js/main.js"></script>
<script type="module" src="components/mercado/js/cartManager.js"></script>
<script type="module" src="components/mercado/js/productManager.js"></script>