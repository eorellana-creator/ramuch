# Auditoría técnica de RAMUCH

Fecha de inicio: 2026-08-04.

Alcance: aplicaciones propias `admin/` y `socios/`.

Fuera de alcance: WordPress (`wp-*`), imágenes, documentos de usuarios, hojas de cálculo y bases exportadas.

Este documento es el tablero de seguimiento para errores funcionales, estabilidad y riesgos técnicos. La bitácora general permanece en `PROJECT.md`.

## Estados

- `[ ]` Pendiente.
- `[~]` En análisis o prueba.
- `[x]` Corregido y verificado.
- `Histórico`: aparece en logs locales antiguos y debe confirmarse con logs actuales.

## Resumen de la evaluación inicial

- 428 PHP propios validados sin errores de sintaxis.
- 40 JavaScript funcionales validados sin errores de sintaxis, excluyendo dependencias y copias `old/back`.
- 24 logs locales inventariados; 19 contienen warnings o errores históricos.
- Los logs locales llegan principalmente hasta enero de 2026: no demuestran por sí solos el estado actual de producción.
- 61 PHP se llaman `old` o `back` y deben considerarse superficie ejecutable hasta retirarlos del servidor.
- 43 PHP activan `display_errors` o `error_reporting(E_ALL)`; 20 manejan cargas de archivos y 21 contienen configuración de correo o contraseñas incorporada. Este informe no reproduce secretos.

## P0 — Prioridad crítica

### Acceso y sesiones

- [ ] Separar sesiones de `/admin` y `/socios`; hoy comparten variables y pueden mezclar roles al usar ambos portales en el mismo navegador.
- [ ] Inventariar endpoints directos y exigir autenticación/autorización dentro de cada archivo, no sólo en el menú o controlador.
- [ ] Incorporar CSRF en todas las operaciones que crean, modifican, pagan, aprueban, rechazan o eliminan. La nueva Intranet ya lo aplica.
- [ ] Cambiar operaciones mutables que usan `GET` a `POST`; hay casos en préstamos, cursos y extensiones.
- [ ] Retirar la impresión de datos de sesión en la consola del dashboard administrativo.

### Datos sensibles y credenciales

- [ ] Sacar credenciales de base de datos y correo del código, migrarlas a configuración privada y rotarlas.
- [ ] Desactivar `display_errors` en producción y escribir logs fuera del directorio público.
- [ ] Impedir descarga directa de logs, fotografías, certificados, comprobantes, PDFs y respaldos.
- [ ] Revisar secretos y datos ya presentes en Git; `.gitignore` no corrige el historial.

### Autenticación

- [ ] Migrar contraseñas MD5 a `password_hash()`/`password_verify()` con actualización progresiva al iniciar sesión.
- [ ] Ejecutar `session_regenerate_id()` después de cada login.
- [ ] Definir expiración, cierre y revocación consistente de sesiones administrativas y de socios.

## P1 — Prioridad alta

### Evidencia histórica de errores

- [ ] Revisar `admin/components/cron/`: registra históricamente `session_start()` después de cabeceras, resultados nulos y cargadores incompatibles con PHP moderno.
- [ ] Hacer que cron no dependa de sesiones web y que sus logs no contengan datos personales.
- [ ] Reemplazar includes relativos frágiles por rutas con `__DIR__`; logs de `admin/` y `socios/` muestran miles de búsquedas fallidas de configuración y conexión.
- [x] Corregir búsqueda y ordenamiento de solicitudes de equipo con columnas SQL inexistentes. Commit `fbb6b94`, publicado en producción.
- [x] Corregir JSON inválido del módulo de deudas.
- [ ] Sustituir JSON manual restante, especialmente `admin/components/equipo/models/equipo_list_procesa.php`.
- [ ] Validar resultados SQL antes de leer propiedades; hay errores históricos por relaciones nulas en socios, usuarios, equipos y cron.

### Formularios y persistencia

- [ ] Inventariar cada formulario: permiso, método HTTP, validación servidor, CSRF, archivos aceptados y tablas afectadas.
- [ ] Reemplazar SQL concatenado por consultas preparadas.
- [ ] Validar obligatoriamente en servidor; no depender de JavaScript, `onblur` o atributos HTML.
- [ ] Auditar las 20 rutas de carga: MIME real, extensión, tamaño, nombre, ubicación no ejecutable y autorización de descarga.
- [ ] Usar transacciones cuando una operación actualiza varias tablas y comprobar filas afectadas.
- [ ] Unificar respuestas AJAX y códigos HTTP.

## P2 — Prioridad media

- [ ] Clasificar los 61 PHP `old/back`; retirar del servidor los no utilizados y conservar historia sólo en Git.
- [ ] Excluir `__MACOSX` y archivos `._*` del despliegue.
- [ ] Evitar inclusiones múltiples de jQuery/Bootstrap dentro de vistas.
- [ ] Unificar zona horaria; los logs mezclan `America/Chicago` y `America/Santiago`.
- [ ] Sustituir supresión con `@` por validaciones explícitas.
- [ ] Definir versión soportada de PHP y pruebas de compatibilidad.

## Hallazgos funcionales pendientes

- [ ] Dashboard “Total Deudas”: confirmar si los tipos correctos son `1,2,6` o `1,3,6`; comentario y consulta no coinciden.
- [ ] Aclarar que “Total Deudas” suma deudas activas vencidas hasta ayer para usuarios vigentes seleccionados, no todas las deudas.
- [ ] Confirmar cómo se reflejan abonos parciales.
- [ ] Revisar conteos, búsqueda, ordenamiento y paginación de todos los DataTables.
- [ ] Probar usuarios eliminados y relaciones huérfanas en deudas, préstamos, perfiles y documentos.
- [ ] Confirmar que staging nunca envíe correos reales ni ejecute pagos reales.

## Funcionalidades recientes

### Intranet de Directiva

- [~] Acceso para rol `Administrador de Socios` y desarrollador ID `1`.
- [~] CRUD con descarte lógico e historial de creación, edición, estados y pago.
- [~] Flujo: solicitada → valorizada → aprobada → en desarrollo → realizada → aprobación final.
- [ ] Probar creación, edición, descarte y pago con ambos perfiles.
- [ ] Probar HTTP 403 con un usuario no autorizado.
- [ ] Probar dos usuarios cambiando simultáneamente la misma solicitud.
- [ ] Respaldar y revisar tablas antes de producción.

### Inventario de equipos

- [~] Listado, alta y edición desplegados sólo en staging.
- [ ] Probar alta con y sin imagen.
- [ ] Probar edición de equipos prestados y disponibles.
- [ ] Confirmar que editar inventario no altere préstamos históricos.

## Plan de pruebas funcionales

- [ ] Preparar cuentas por rol: socio, administrador de socios, administrador de equipos y desarrollador.
- [ ] Crear matriz de rutas y permisos esperados.
- [ ] Probar login, logout, recuperación y cambio de contraseña.
- [ ] Probar altas, ediciones, búsquedas, ordenamiento, paginación y exportaciones.
- [ ] Probar préstamos, devoluciones, extensiones y solicitudes múltiples.
- [ ] Probar deudas, pagos y condonaciones; Flow sólo en entorno seguro.
- [ ] Probar cargas y descargas con formatos válidos e inválidos.
- [ ] Revisar consola, XHR y logs después de cada flujo.
- [ ] Registrar cada error con ruta, rol, pasos, resultado esperado y resultado observado.

## Límites de esta evaluación

La primera evaluación es estática y usa el clon y logs históricos. No se realizaron escrituras en producción, pagos, envíos reales ni cambios de datos. Un hallazgo se considerará activo en producción sólo después de reproducirlo o comprobarlo en logs actuales.
