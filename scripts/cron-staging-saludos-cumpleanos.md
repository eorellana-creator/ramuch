# Saludos de cumpleaños en staging

El proceso está preparado exclusivamente para el árbol `staging.ramuch.cl`.
No debe programarse ni copiarse a producción durante las pruebas.

## Revisión sin enviar correos

```bash
/usr/local/bin/php -q /home/ramuchcl/staging.ramuch.cl/admin/components/cron/enviar_saludos_cumpleanos.php --dry-run
```

La revisión muestra solamente el número de candidatos y sus identificadores y nombres. No envía correos ni crea marcas de envío.

## Programación propuesta (todavía no ejecutar)

```cron
0 9 * * * /usr/local/bin/php -q /home/ramuchcl/staging.ramuch.cl/admin/components/cron/enviar_saludos_cumpleanos.php >> /home/ramuchcl/staging.ramuch.cl/admin/components/cron/runtime/cumpleanos-cron.log 2>&1
```

La zona horaria proviene de `admin/configuration.php`. El proceso acepta envíos solamente durante las 09:00 y utiliza archivos de control por fecha y socio para impedir repeticiones, sin agregar tablas a la base de datos.

En staging, `RamuchMailer` reemplaza los destinatarios por el buzón de pruebas configurado. Al llevar esta función a producción se debe retirar primero el bloqueo de ruta incluido en el script y repetir las pruebas correspondientes.
