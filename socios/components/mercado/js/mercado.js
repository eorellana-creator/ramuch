// Configuración de DataTables
const dataTableConfig = {
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
                    `<button class="btn btn-sm btn-primary add-to-cart" onclick="cartManager.add('${row[0]}')">
                        <i class="fas fa-plus"></i>
                    </button>`,
                    ...row
                ];
            });
        }
    }
};

// Sistema de notificaciones
const notifications = {
    showSuccess: function (message) {
        Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: message,
            timer: 2000,
            showConfirmButton: false
        });
    },
    showError: function (message) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message
        });
    },
    showWarning: function (message) {
        Swal.fire({
            icon: 'warning',
            title: 'Advertencia',
            text: message
        });
    }
};

// Inicialización y eventos principales
$(document).ready(function () {
    // Validar e inicializar DataTable
    if (!$.fn.DataTable.isDataTable('#tabla')) {
        $('#tabla').DataTable(dataTableConfig);
    }

    // Eventos de la tabla
    const table = $('#tabla').DataTable();
    table.on('draw', function () {
        $('.sel2-basic-single').select2();
        initializeModals();
    });

    // Inicializar componentes UI
    $('[data-toggle="tooltip"]').tooltip();
    $('i.fa').popover({ trigger: 'hover' });

    // Inicializar Select2
    $('.sel2-basic-single').select2({
        placeholder: 'Seleccione una opción',
        width: '100%'
    });

    // Inicializar modales y handlers
    initializeModals();

    // Validación de imágenes
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

    // Función para abrir el modal de imagen
    function openImageModal(imageUrl) {
        const modal = $('#imageModal');
        const modalImage = modal.find('#modalImage');
        modalImage.attr('src', imageUrl);
        modal.modal('show');
    }

// Inicialización de modales
function initializeModals() {
    // Modal de detalles
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


    // Modal de imagen
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

    // Modal del carrito
    $('#carritoModal').on('show.bs.modal', function () {
        cartManager.loadCartDetails();
    });

    // Manejador para cambios en la cantidad
    $(document).on('change', '.quantity-input', function () {
        const productId = $(this).data('product-id');
        const quantity = parseInt($(this).val());
        if (quantity > 0) {
            cartManager.updateQuantity(productId, quantity);
        }
    });

    // Manejador para el botón de procesar compra
    $('#btnProcesarCompra').on('click', function () {
       
                procesarPago();
   
    });
}

function procesarPago() {
    // Obtener los datos del carrito (puedes usar AJAX para obtener los datos del carrito desde el servidor)
    var carrito = obtenerDatosCarrito(); // Esta función debe devolver los datos del carrito en formato JSON

    // Depuración: Verificar los datos del carrito
    console.log('Datos del carrito:', carrito);

    // Enviar los datos del carrito al servidor para procesar el pago
    //console.log('Ruta de procesar_pago.php:', 'components/mercado/views/procesar_pago.php');

    $.ajax({
        url: "./components/mercado/views/procesar_pago.php",
        type: "post",
        data: JSON.stringify(carrito), // Convertir a JSON  carrito,
        contentType: 'application/json',
        success: function(resp) {
            console.log(resp);
            if (resp.success) {
                window.location.href = resp.redirectUrl + '?token=' + resp.token;
            } else {
                alert('Error al procesar el pago: ' + resp.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error en la solicitud AJAX:', error);
            alert('Error al procesar el pago. Por favor, inténtalo de nuevo.');
        }
    });
}

function obtenerDatosCarrito() {
    var items = [];
    var total = 0;

    // Obtener las filas del carrito (excluyendo la fila de encabezado)
    var rows = document.querySelectorAll('#carritoContent tbody tr');
    console.log('Filas encontradas:', rows.length);

    rows.forEach(function(row, index) {
        var columns = row.querySelectorAll('td');
        console.log(`Fila ${index + 1}:`, columns.length, 'columnas');

        // Asegurarse de que la fila tenga al menos 4 columnas (ID, Nombre, Cantidad, Precio)
        if (columns.length >= 4) {
            var id = columns[0].innerText; // ID del producto
            var name = columns[1].innerText; // Nombre del producto
            var quantity = parseInt(columns[2].innerText); // Cantidad
            var price = limpiarPrecio(columns[3].innerText); // Precio unitario (limpio y convertido a número)

            console.log(`Producto ${index + 1}:`, { id, name, quantity, price });

            // Validar que los datos sean válidos
            if (!isNaN(quantity) && !isNaN(price)) {
                items.push({
                    id: id,
                    name: name,
                    price: price,
                    quantity: quantity
                });

                total += price * quantity; // Calcular el total
            } else {
                console.error(`Datos inválidos en la fila ${index + 1}:`, { id, name, quantity, price });
            }
        } else {
            // Mensaje de depuración si la fila no tiene suficientes columnas
            console.error(`La fila ${index + 1} no tiene suficientes columnas. Columnas encontradas:`, columns.length);
        }
    });

    console.log('Datos del carrito:', { items, total });

    // Devolver los datos del carrito en formato JSON
    return {
        items: items,
        total: total
    };
}

function limpiarPrecio(precio) {
    // Eliminar símbolos de moneda y separadores de miles
    return parseFloat(precio.replace(/[^0-9.,]/g, '').replace('.', ''));
}

// Funciones globales
window.validaImagen = function (e) {
    const fileExtension = ['png', 'jpeg', 'jpg', 'gif'];
    if ($.inArray($(e).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
        notifications.showError('El archivo debe ser una imagen.');
        $(e).val('');
        return false;
    }
    return true;
};