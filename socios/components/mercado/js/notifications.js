export const notifications = {
    showSuccess: function(message) {
        Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: message,
            timer: 2000,
            showConfirmButton: false
        });
    },
    showError: function(message) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message
        });
    },
    showWarning: function(message) {
        Swal.fire({
            icon: 'warning',
            title: 'Advertencia',
            text: message
        });
    }
};

window.notifications = notifications;