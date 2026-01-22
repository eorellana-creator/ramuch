const productManager = {
    init: function() {
        this.initializeEventListeners();
        this.loadCategorias();
    },

    initializeEventListeners: function() {
        // Manejador para el formulario de producto
        $('#formProducto').off('submit'); // Elimina cualquier evento previo
        $('#formProducto').on('submit', (e) => {
            e.preventDefault(); // Prevenir el envío predeterminado del formulario
            this.saveProduct();
        });

        // Preview de imagen
        $('#imagen').on('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview').html(`
                        <img src="${e.target.result}" class="img-thumbnail" style="max-height: 200px;">
                    `);
                };
                reader.readAsDataURL(file);
            }
        });

        // Validación de campos numéricos
        $('.numeric-input').on('input', function() {
            this.value = this.value.replace(/[^0-9.]/g, '');
        });
    },

    loadCategorias: function() {
        $.ajax({
            url: 'components/mercado/models/get_categorias.php',
            type: 'GET',
            success: (response) => {
                const categorias = JSON.parse(response);
                let options = '<option value="">Seleccione una categoría</option>';
                categorias.forEach(categoria => {
                    options += `<option value="${categoria.id}">${categoria.nombre}</option>`;
                });
                $('#categoria_id').html(options);
            },
            error: () => {
                notifications.showError('Error al cargar las categorías');
            }
        });
    },

    saveProduct: function() {
        const formData = new FormData($('#formProducto')[0]);
        const submitButton = $('#formProducto button[type="submit"]'); // Seleccionar botón de guardar

        submitButton.prop('disabled', true).text('Guardando...'); // Desactivar botón mientras se procesa

        $.ajax({
            url: 'components/mercado/models/save_producto.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: (response) => {
                if (response.success) {
                    notifications.showSuccess(response.message || 'Producto guardado exitosamente');
                    $('#modalProducto').modal('hide');
                    $('#tabla').DataTable().ajax.reload();
                    this.resetForm();
                } else {
                    notifications.showError(response.message || 'Error al guardar el producto');
                }
            },
            error: (jqXHR, textStatus, errorThrown) => {
                console.error('AJAX Error:', textStatus, errorThrown, jqXHR.responseText);
                notifications.showError('Error de conexión: verifica la consola para más detalles');
            }
        });
        
    },

    resetForm: function() {
        $('#formProducto')[0].reset();
        $('#imagePreview').empty();
    }
};

// Exponer el productManager globalmente
window.productManager = productManager;

// Inicializar cuando el documento esté listo
$(document).ready(() => {
    productManager.init();
});
