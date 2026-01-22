<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario con reCAPTCHA</title>
    <!-- Clave del sitio reCAPTCHA -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <!-- Incluir jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h2>Formulario con reCAPTCHA</h2>
    <form id="captcha-form" method="POST">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required><br><br>

        <label for="email">Correo Electrónico:</label>
        <input type="email" id="email" name="email" required><br><br>

        <!-- Aquí se inserta el captcha de Google -->
        <div class="g-recaptcha" data-sitekey="6LfEwTkqAAAAAES8d1xsGnu9cQ52GunART1qltZM"></div><br>

        <input type="submit" value="Enviar">
    </form>

    <div id="result"></div>

    <script>
        $(document).ready(function() {
            $('#captcha-form').on('submit', function(e) {
                e.preventDefault(); // Evitar recarga de la página
                var formData = $(this).serialize(); // Serializar los datos del formulario
                
                $.ajax({
                    url: 'verificar_captcha.php', // Archivo PHP donde se valida el captcha
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        $('#result').html(response); // Mostrar el mensaje de validación
                    },
                    error: function() {
                        $('#result').html('Error al enviar el formulario.');
                    }
                });
            });
        });
    </script>
</body>
</html>

