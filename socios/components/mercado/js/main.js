// Main application initialization
$(document).ready(function () {
    // Initialize Bootstrap components
    $('[data-toggle="tooltip"]').tooltip();
    $('[data-toggle="popover"]').popover();

    // Initialize Select2
    $('.sel2-basic-single').select2({
        placeholder: 'Seleccione una opción',
        width: '100%'
    });

    // Validate and initialize DataTable
    if (!$.fn.DataTable.isDataTable('#tabla')) {
        $('#tabla').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
            },
            ordering: true,
            processing: true,
            serverSide: true,
            responsive: false,
            order: [[1, 'desc']],
            pageLength: 25,
            columnDefs: [
                { orderable: false, targets: [0, 2, 9, 10] }
            ],
            ajax: {
                url: 'components/mercado/models/mercado_list_procesa.php',
                type: 'POST',
                dataSrc: function (json) {
                    if (!json.data) {
                        console.error('Invalid JSON response:', json);
                        return [];
                    }
                    return json.data.map(row => {
                        return [
                            `<button class="btn btn-sm btn-primary add-to-cart" data-product-id="${row[0]}">
                                <i class="fas fa-plus"></i>
                            </button>`,
                            ...row
                        ];
                    });
                }
            }
        });
    }

    // Initialize modal manager
    modalManager.init();

    // Add to cart button handler
    $(document).off('click', '.add-to-cart');
    $(document).on('click', '.add-to-cart', function (e) {
        e.preventDefault();
        const productId = $(this).data('product-id');
        cartManager.add(productId);
    });

    // Image validation
    $('#imagen').on('change', function () {
        if (!validaImagen(this)) {
            return;
        }
        const preview = $('#imagePreview');
        preview.empty();

        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.html(`<img src="${e.target.result}" class="img-thumbnail" style="max-height: 200px;">`);
            };
            reader.readAsDataURL(this.files[0]);
        }
    });
});

// Image validation helper
function validaImagen(e) {
    const fileExtension = ['png', 'jpeg', 'jpg', 'gif'];
    if ($.inArray($(e).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
        notifications.showError('El archivo debe ser una imagen.');
        $(e).val('');
        return false;
    }
    return true;
}

function confirmDelete(button) {
    const productId = $(button).data('product-id');
    console.log('ID del producto a eliminar:', productId); // Para depuración
    
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción no se puede deshacer.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            deleteProduct(productId);
        }
    });
}

function deleteProduct(productId) {
    $.ajax({
        url: 'components/mercado/models/delete_producto.php',
        type: 'POST',
        data: { id: productId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire(
                    'Eliminado',
                    response.message || 'El producto ha sido eliminado.',
                    'success'
                );
                $('#tabla').DataTable().ajax.reload();
            } else {
                Swal.fire(
                    'Error',
                    response.message || 'Hubo un problema al eliminar el producto.',
                    'error'
                );
            }
        },
        error: function(xhr, status, error) {
            console.error('Error en la solicitud AJAX:', error);
            Swal.fire(
                'Error',
                'No se pudo conectar al servidor.',
                'error'
            );
        }
    });
}