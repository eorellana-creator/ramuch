// Gestión de modales para productos
const modalManager = {
    init: function () {
        this.initCartModal();
        this.initImageModal();
        this.initDetailModal();
        this.initEditModal();
        this.initEventHandlers();
    },

    initCartModal: function () {
        $('#carritoModal').on('show.bs.modal', function () {
            cartManager.loadCartDetails();
        });
    },

    initImageModal: function () {
        $('#imageModal').on('show.bs.modal', function (event) {
            const modal = $(this);
            const imageContainer = modal.find('.image-container');

            setTimeout(() => {
                const containerWidth = imageContainer.width();
                const containerHeight = imageContainer.height();
                const scrollLeft = (imageContainer[0].scrollWidth - containerWidth) / 2;
                const scrollTop = (imageContainer[0].scrollHeight - containerHeight) / 2;

                imageContainer.scrollLeft(scrollLeft);
                imageContainer.scrollTop(scrollTop);
            }, 100);
        });
    },

    initDetailModal: function () {
        $('#detalleModal').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const token = button.data('token');

            $.ajax({
                url: 'components/mercado/models/get_producto_details.php',
                type: 'GET',
                data: { token: token },
                success: function (response) {
                    $('#detalleModal .modal-body').html(response);
                },
                error: function () {
                    notifications.showError('Error al cargar los detalles del producto');
                }
            });
        });
    },

    initEditModal: function() {
        // Evento para abrir modal de edición
        $(document).on('click', '.edit-product', function(e) {
            e.preventDefault();
            const productId = $(this).data('product-id');
            modalManager.loadProductData(productId);
        });

        // Evento para guardar cambios
        $('#formEditProducto').on('submit', function(e) {
            e.preventDefault();
            modalManager.saveProductChanges(this);
        });

        // Cargar categorías al abrir el modal de edición
        $('#modalEditProducto').on('show.bs.modal', function() {
            modalManager.loadCategories();
        });
    },

    loadCategories: function() {
        $.ajax({
            url: 'components/mercado/models/get_categorias.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                let options = '<option value="">Seleccione una categoría</option>';
                response.forEach(function(categoria) {
                    options += `<option value="${categoria.id}">${categoria.nombre}</option>`;
                });
                $('#edit_categoria_id').html(options);
            },
            error: function() {
                notifications.showError('Error al cargar las categorías');
            }
        });
    },

    loadProductData: function(productId) {
        $.ajax({
            url: 'components/mercado/models/get_producto_data.php',
            type: 'POST',
            data: { id: productId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    modalManager.fillEditForm(response.data);
                    $('#modalEditProducto').modal('show');
                } else {
                    Swal.fire('Error', 'No se pudo cargar la información del producto', 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Error al conectar con el servidor al leer producto.', 'error');
            }
        });
    },

    fillEditForm: function(data) {
        $('#edit_product_id').val(data.id);
        $('#edit_nombre').val(data.nombre);
        $('#edit_precio').val(data.precio);
        $('#edit_stock').val(data.stock);
        $('#edit_stock_minimo').val(data.stock_minimo);
        $('#edit_categoria_id').val(data.categoria_id);
        $('#edit_marca').val(data.marca);
        $('#edit_modelo').val(data.modelo);
        $('#edit_estado').val(data.estado);
        $('#edit_talla').val(data.talla);
        $('#edit_color').val(data.color);
        $('#edit_descuento').val(data.descuento);
        $('#edit_descripcion').val(data.descripcion);
        
        if (data.imagen_nombre) {
            $('#currentImagePreview').html(`<img src="components/mercado/images/productos/${data.imagen_nombre}" class="img-thumbnail" style="max-height: 200px;">`);
        }
    },

    saveProductChanges: function(form) {
        const formData = new FormData(form);
        
        $.ajax({
            url: 'components/mercado/models/update_producto.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire('¡Éxito!', 'Producto actualizado correctamente', 'success');
                    $('#modalEditProducto').modal('hide');
                    $('#tabla').DataTable().ajax.reload();
                } else {
                    Swal.fire('Error', response.message || 'Error al actualizar el producto', 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Error al conectar con el servidor al grabar producto.', 'error');
            }
        });
    },

    initEventHandlers: function () {
        $(document).on('change', '.quantity-input', function () {
            const productId = $(this).data('product-id');
            const quantity = parseInt($(this).val());
            if (quantity > 0) {
                cartManager.updateQuantity(productId, quantity);
            }
        });

        $('#btnProcesarCompra').on('click', function () {
            //Swal.fire({
                //title: '¿Confirmar compra?',
                //text: "¿Desea proceder con la compra de estos productos?",
                //icon: 'question',
                //showCancelButton: true,
                //confirmButtonColor: '#3085d6',
                //cancelButtonColor: '#d33',
                //confirmButtonText: 'Sí, proceder',
                //cancelButtonText: 'Cancelar'
            //}).then((result) => {
                //if (result.isConfirmed) {
                //    cartManager.clearCart();
                //    notifications.showSuccess('Compra procesada exitosamente');
                //    $('#carritoModal').modal('hide');
               //}
            //});
        });
    }
};

// Hacer accesible modalManager globalmente
window.modalManager = modalManager;