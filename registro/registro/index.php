<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro en Club de Montaña</title>
    <script src='https://www.google.com/recaptcha/api.js?render=6Ldsrc0pAAAAAFINb8eGvyLZNUv8OnpyqWzOxWtW'> </script> 
    <script> grecaptcha.ready(function() { 
        grecaptcha.execute('6Ldsrc0pAAAAAFINb8eGvyLZNUv8OnpyqWzOxWtW', {
            action: 'ejemplo'
        }) .then(function(token) {
            var recaptchaResponse = document.getElementById('recaptchaResponse'); recaptchaResponse.value = token; 
        });
        }); 
    </script>    
</head>
<body>
    <form action="procesar_registro.php" method="post">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required><br>
        
        <label for="rut">RUT:</label>
        <input type="text" id="rut" name="rut" required><br>
        
        <label for="telefono">Teléfono:</label>
        <input type="text" id="telefono" name="telefono" required><br>
        
        <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required><br>
        
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br>
        
        <div class="g-recaptcha" data-sitekey="6Ldsrc0pAAAAAFINb8eGvyLZNUv8OnpyqWzOxWtW"></div><br>
        <input type="hidden" name="recaptcha_response" id="recaptchaResponse">
        
        
        <button type="submit">Registrarse</button>
    </form>
</body>
</html>
