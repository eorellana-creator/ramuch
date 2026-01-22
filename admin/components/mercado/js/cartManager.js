// Cart Manager Module
const cartManager = {
    cart: [],

    // Agregar un producto al carrito
    add: function(productId) {
        const product = this.findProduct(productId);
        if (product) {
            product.quantity += 1;
        } else {
            this.cart.push({
                id: productId,
                quantity: 1
            });
        }
        this.updateCartCount();
        // notifications.showSuccess('Producto agregado al carrito');
    },

    // Encontrar un producto en el carrito
    findProduct: function(productId) {
        return this.cart.find(item => item.id === productId);
    },

    // Actualizar el contador de productos en el carrito
    updateCartCount: function() {
        const totalItems = this.cart.reduce((sum, item) => sum + item.quantity, 0);
        $('#cartCount').text(totalItems);
    },

    // Cargar los detalles del carrito en la interfaz
    loadCartDetails: function() {
        if (this.cart.length === 0) {
            $('#carritoContent').html('<p class="text-center">El carrito está vacío</p>');
            return;
        }

        // Enviar el carrito al servidor y actualizar el contenido del modal
        $.ajax({
            url: 'components/mercado/models/get_cart_details.php',
            type: 'POST',
            data: { cart: JSON.stringify(this.cart) },
            success: (response) => {
                $('#carritoContent').html(response);
                this.updateCartCount();
                this.attachRemoveItemEvent(); // Asegurar que los botones "Eliminar" tengan el evento correcto
            },
            error: () => {
                // notifications.showError('Error al cargar los detalles del carrito');
            }
        });
    },

    // Actualizar la cantidad de un producto en el carrito
    updateQuantity: function(productId, quantity) {
        const product = this.findProduct(productId);
        if (product) {
            product.quantity = quantity;
            this.updateCartCount();
            this.loadCartDetails();
        }
    },

    // Eliminar un producto del carrito
    removeItem: function(productId) {
        this.cart = this.cart.filter(item => item.id !== productId);
        this.updateCartCount();
        this.loadCartDetails();
    },

    // Vaciar completamente el carrito
    clearCart: function() {
        this.cart = [];
        this.updateCartCount();
        this.loadCartDetails();
    },

    // Adjuntar el evento "Eliminar" a los botones en la tabla
    attachRemoveItemEvent: function() {
        const _this = this; // Referencia al contexto de cartManager
        $(document).on('click', '.remove-item', function() {
            const productId = $(this).data('product-id');

            // Confirmar eliminación con SweetAlert2 (si está integrado)
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Este producto será eliminado del carrito.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    _this.removeItem(productId); // Llamar al método para eliminar el producto
                    Swal.fire('Eliminado', 'El producto ha sido eliminado del carrito.', 'success');
                }
            });
        });
    }
};

// Hacer que cartManager sea accesible globalmente
window.cartManager = cartManager;

// Inicialización automática para cargar detalles del carrito al abrir el modal
$(document).ready(function() {
    cartManager.loadCartDetails();
});




// Cart Manager Module
/*
const cartManager = {
    cart: [],
    
    add: function(productId) {
        const product = this.findProduct(productId);
        if (product) {
            product.quantity += 1;
        } else {
            this.cart.push({
                id: productId,
                quantity: 1
            });
        }
        this.updateCartCount();
        notifications.showSuccess('Producto agregado al carrito');
    },

    findProduct: function(productId) {
        return this.cart.find(item => item.id === productId);
    },

    updateCartCount: function() {
        const totalItems = this.cart.reduce((sum, item) => sum + item.quantity, 0);
        $('#cartCount').text(totalItems);
    },

    loadCartDetails: function() {
        if (this.cart.length === 0) {
            $('#carritoContent').html('<p class="text-center">El carrito está vacío</p>');
            return;
        }

        $.ajax({
            url: 'components/mercado/models/get_cart_details.php',
            type: 'POST',
            data: { cart: JSON.stringify(this.cart) },
            success: (response) => {
                $('#carritoContent').html(response);
                this.updateCartCount();
            },
            error: () => {
                notifications.showError('Error al cargar los detalles del carrito');
            }
        });
    },

    updateQuantity: function(productId, quantity) {
        const product = this.findProduct(productId);
        if (product) {
            product.quantity = quantity;
            this.updateCartCount();
            this.loadCartDetails();
        }
    },

    removeItem: function(productId) {
        this.cart = this.cart.filter(item => item.id !== productId);
        this.updateCartCount();
        this.loadCartDetails();
    },

    clearCart: function() {
        this.cart = [];
        this.updateCartCount();
        this.loadCartDetails();
    }
};

// Make cartManager globally accessible
window.cartManager = cartManager;
*/