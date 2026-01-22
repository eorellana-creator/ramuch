<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ejemplo de Tabla con Botón</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.prestar-btn').click(function() {
                var id = $(this).data('id');
                var boton = $(this);
                
                $.ajax({
                    url: 'prestar_articulo.php',
                    method: 'POST',
                    data: { id: id },
                    success: function(response) {
                        if (response.success) {
                            boton.prop('disabled', true).text('Prestado');
                        } else {
                            alert('Error al prestar el artículo');
                        }
                    },
                    error: function() {
                        alert('Error en la solicitud AJAX');
                    }
                });
            });
        });
    </script>
</head>
<body>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Artículo</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Simulación de datos de la base de datos
            $articulos = [
                ['id' => 1, 'nombre' => 'Artículo 1'],
                ['id' => 2, 'nombre' => 'Artículo 2'],
                ['id' => 3, 'nombre' => 'Artículo 3'],
            ];

            foreach ($articulos as $articulo): ?>
            <tr id="fila-<?php echo $articulo['id']; ?>">
                <td><?php echo $articulo['id']; ?></td>
                <td><?php echo $articulo['nombre']; ?></td>
                <td>
                    <button class="prestar-btn" data-id="<?php echo $articulo['id']; ?>">Prestar</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    
</body>
</html>
