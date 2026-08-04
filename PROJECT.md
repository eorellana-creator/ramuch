# Proyecto RAMUCH

Este archivo es la bitacora tecnica principal del proyecto. Debe actualizarse en cada cambio relevante, antes de crear el commit correspondiente.

## Estado inicial

Fecha del levantamiento: 2026-07-19.

El directorio contiene una aplicacion PHP historica compuesta por:

- archivos de entrada de una instalacion WordPress en la raiz;
- un panel administrativo propio en `admin/`;
- un portal de socios en `socios/`;
- un flujo de registro en `registro/`;
- un flujo de pagos e integracion con Flow en `pagar/`;
- librerias copiadas dentro del proyecto, entre ellas PHPMailer, PHPExcel, FPDF, Bootstrap y DataTables.

La copia local ocupa aproximadamente 1,6 GiB, incluyendo `.git`. El repositorio contiene 21.777 archivos rastreados y el paquete del historial Git ocupa aproximadamente 534 MiB. Los directorios mas pesados son `admin/` (~748 MiB), `socios/` (~246 MiB), `registro/` (~49 MiB) y `pagar/` (~42 MiB).

La rama actual es `main`, enlazada a `origin/main`. Al comenzar este levantamiento no habia cambios pendientes. No se realizo staging, commit ni despliegue.

## Hallazgos y riesgos

### Datos y archivos grandes

El repositorio mezcla codigo fuente con archivos operacionales: fotografias de perfil y equipos, documentos PDF, archivos de deudas y logs. Parte de ese contenido aparece duplicado entre `admin/` y `socios/`. No se encontraron volcados `.sql` o `.dump` en la copia examinada, pero la base de datos de produccion debe considerarse externa al repositorio y nunca debe incorporarse a Git.

El `.gitignore` creado en este levantamiento evita que se agreguen nuevos volcados, respaldos, logs, archivos subidos por usuarios y dependencias de Node. Sin embargo, Git sigue rastreando los archivos que ya estaban comprometidos. Limpiar el historial requiere una operacion posterior, planificada y respaldada; no debe improvisarse.

### Credenciales

`wp-config.php`, `wp-config_back.php` y archivos `conexionMysql.php` contienen o pueden contener datos de conexion. Actualmente varios de ellos ya forman parte del historial. Deben migrarse a variables de entorno o a configuracion privada del servidor y luego rotarse las credenciales expuestas. Agregarlos al `.gitignore` solamente protege copias futuras que aun no esten rastreadas.

Nunca se deben anotar contrasenas, tokens, llaves de Flow, accesos FTP/SFTP/SSH ni datos personales en esta bitacora, mensajes de commit o archivos versionados.

### Seguridad y antiguedad tecnica

Se observaron autenticaciones que usan MD5 para contrasenas y numerosos accesos directos a `$_POST`. Antes de ampliar funciones conviene revisar autenticacion, validacion de entradas, consultas SQL, manejo de sesiones, carga de archivos y versiones de librerias. La migracion de contrasenas debe mantener compatibilidad temporal para no bloquear a usuarios existentes.

La raiz contiene archivos de WordPress, pero no aparecen los directorios normales `wp-admin/`, `wp-includes/` y `wp-content/` en esta copia. Por ello no se puede confirmar una instalacion WordPress completa ni su version desde el contenido actual.

### Residuos historicos

Existen archivos `*_old.php`, `*_back.php`, logs, metadatos `__MACOSX` y artefactos de actualizadores. A futuro, el historial Git debe reemplazar las copias manuales `old/back`; primero hay que verificar que ninguna sea cargada directamente por la aplicacion.

## Reglas de trabajo

1. Trabajar en una rama por cambio y revisar `git status` y `git diff` antes de cualquier staging.
2. No ejecutar `git add .` hasta terminar la clasificacion inicial del contenido ya rastreado.
3. No versionar bases de datos, respaldos, logs, archivos subidos por usuarios, secretos ni configuracion de produccion.
4. Hacer un respaldo verificable de archivos y base de datos antes de cada despliegue.
5. Probar localmente o en staging; produccion no debe ser el entorno de prueba.
6. Usar commits pequenos, descriptivos y reversibles, y registrar aqui su objetivo y resultado.
7. Separar el despliegue del commit: un commit no debe publicar automaticamente hasta que exista un flujo probado, con aprobacion, respaldo y rollback.

## Camino hacia el despliegue automatico

La sustitucion de FTP manual debe hacerse por etapas:

1. identificar hosting, version de PHP, servidor web, ruta publica y forma de acceso disponible (preferentemente SSH/SFTP o un agente de CI; evitar FTP sin cifrado);
2. inventariar que archivos son codigo desplegable y cuales pertenecen a produccion o a usuarios;
3. sacar credenciales del repositorio y rotarlas;
4. crear un entorno de staging equivalente a produccion;
5. preparar un script reproducible que transfiera solo codigo, conserve datos y configuracion del servidor y permita modo de simulacion;
6. crear respaldo previo, verificacion posterior y procedimiento de rollback;
7. conectar el script a una accion manual aprobada o a una etiqueta de version; no desplegar por cada commit de manera inmediata.

Hasta completar estas condiciones no se debe automatizar el paso a produccion.

## Bitacora

### 2026-08-04 - Inicio de auditoría integral de admin y socios

- Se creó `SITE_AUDIT.md` como tablero de errores, riesgos, prioridades y pruebas pendientes.
- Se validaron 428 PHP propios y 40 JavaScript funcionales sin errores de sintaxis.
- Se inventariaron 24 logs locales; 19 contienen evidencia histórica, pero no representan necesariamente el estado actual de producción.
- Los grupos principales son rutas relativas, sesiones después de cabeceras, resultados SQL nulos, JSON manual y componentes antiguos incompatibles con PHP moderno.
- Se priorizaron autorización de endpoints, CSRF, separación de sesiones, consultas preparadas, credenciales privadas, autenticación MD5, cargas y copias ejecutables `old/back`.
- No se modificó funcionalidad ni producción durante esta evaluación.

### 2026-08-04 - Intranet privada y solicitudes de actualizaciones

- Se creó una Intranet para el rol `Administrador de Socios` y el desarrollador ID `1`.
- Incluye dashboard y CRUD con estados, valorización, aprobación, ejecución, revisión final y pago.
- El descarte es lógico; creación, edición, transición, descarte y pago quedan auditados con usuario y fecha.
- Los commits `230c0a2` y `2c491a1` fueron desplegados sólo en staging y verificados por descarga.
- Producción no fue modificada; quedan pendientes pruebas funcionales y de permisos.

### 2026-08-03 - Prevención de solicitudes de equipo sin socio

- Se confirmaron en producción dos solicitudes inválidas con `id_usuario_prestamo = 0`; fueron rechazadas manualmente antes de modificar el código.
- El endpoint vigente ahora confirma en la base que el usuario de sesión existe y está vigente antes de insertar cualquier solicitud.
- Se protegieron también las dos rutas heredadas capaces de insertar solicitudes sin validar la sesión.
- El listado administrativo identifica las relaciones inválidas y deshabilita su aceptación en lugar de mostrar una celda vacía.
- El portal informa al socio si su sesión dejó de ser válida durante una solicitud.
- Los cambios quedan pendientes de commit, despliegue y comprobación funcional.

### 2026-07-29 - Indicador de procesamiento en tabla de equipos

- La tabla muestra una capa semitransparente, spinner y texto “Procesando…” mientras espera operaciones remotas.
- El indicador se activa al ordenar, buscar o cambiar de página y desaparece al finalizar DataTables.
- Se creó el commit `e09a173`, se desplegaron JavaScript y CSS en staging y se verificaron mediante descarga y SHA-256.
- Producción no fue modificada.

### 2026-07-29 - Fechas de devolución separadas en el listado

- La columna “Préstamo a” ahora muestra únicamente el nombre del socio.
- Se agregó “Fecha devolución” con la fecha vigente, la primera extensión y la segunda extensión.
- Se ajustaron los índices de DataTables, el ordenamiento por fecha y la exportación Excel.
- Se creó el commit `813cb03`, se desplegaron los tres archivos afectados en staging y se verificaron mediante descarga y SHA-256.
- Producción no fue modificada.

### 2026-07-29 - Flujo de préstamos y listado promovidos a producción

- Se compararon y respaldaron los 12 archivos de aplicación afectados antes del despliegue.
- Todos los archivos de producción coincidían con la base anterior de `main`; no se detectaron cambios más nuevos.
- Los commits `d26fb02` a `7bde477` fueron promovidos por avance directo a `main` y publicados en GitHub.
- Se desplegaron únicamente los 12 archivos de aplicación de `admin` y `socios`; no se transfirió el script exclusivo de staging.
- WordPress, imágenes, documentos de usuarios, configuración y base de datos no fueron modificados.
- Los 12 archivos se descargaron nuevamente desde producción y coincidieron de forma binaria con `main`.
- La condición de redirección de correo sólo se activa en `staging.ramuch.cl`; producción conserva sus destinatarios normales.
- Queda pendiente la comprobación funcional en navegador.

### 2026-07-29 - Listado administrativo limitado a equipos prestados

- El listado `admin/index.php?component=equipo&view=equipo_list` ahora consulta únicamente equipos con `prestado_a_id_usuario > 0`.
- El orden inicial quedó configurado por nombre del socio en “Préstamo a” de A a Z y, como desempate, nombre del equipo de A a Z.
- Los conteos y la exportación del módulo utilizan el mismo filtro de equipos prestados.
- Se creó el commit `7bde477`, se desplegaron los dos archivos afectados en staging y se verificaron mediante descarga y SHA-256.
- Producción no fue modificada.

### 2026-07-26 - Portal de socios habilitado en staging

- Se neutralizó el envío real de correo en las copias de PHPMailer de `admin` y `socios` cuando el host es exactamente `staging.ramuch.cl`.
- Posteriormente, por decisión del usuario, el bloqueo se reemplazó por una redirección controlada: staging elimina todos los destinatarios, copias y direcciones de respuesta originales, envía exclusivamente al buzón de pruebas autorizado y agrega `[STAGING]` al asunto.
- La redirección quedó versionada en el commit `3685e9c`, desplegada en ambas aplicaciones y verificada mediante descarga y SHA-256.
- Se creó el commit `95bb3f2` con el bloqueo de correo y se desplegó en staging.
- Se creó `scripts/deploy-staging-socios.py` para realizar una transferencia FTPS selectiva, reintentable y sin credenciales incorporadas.
- Se excluyeron fotografías de perfil, imágenes operacionales, productos de mercado, documentos de usuarios, logs, hojas de cálculo, `node_modules` y fuentes de desarrollo no requeridas.
- Se transfirieron 940 archivos y aproximadamente 39 MiB a `/staging.ramuch.cl/socios`.
- La conexión de `socios` se obtuvo directamente desde la conexión ya validada del `admin` de staging; ambas copias remotas resultaron idénticas.
- Los seis archivos críticos del flujo de socios fueron descargados nuevamente y comparados de forma exacta con el repositorio.
- La URL `/socios/` responde con HTTP 401 sin credenciales, confirmando que existe y está cubierta por la protección HTTP del staging.
- El script de despliegue quedó versionado en el commit `3560d35`.
- Queda pendiente comprobar el inicio de sesión y el flujo visual desde un navegador autenticado.
- Producción no fue modificada.

### 2026-07-26 - Consistencia del flujo de préstamos y extensiones

- Se creó la rama `fix/equipo-loan-extension-flow` y el commit `e1ed4ab`, basado en la corrección del préstamo activo.
- La aceptación o rechazo administrativo ahora espera la respuesta AJAX, utiliza POST, bloquea la solicitud durante la operación y sólo procesa registros en estado `solicitado`.
- La creación de solicitudes valida sesión, tokens y fechas, y bloquea el equipo para evitar solicitudes activas simultáneas.
- Las extensiones validan que el préstamo pertenezca al socio autenticado, que el tipo sea válido y que las fechas sean cronológicamente posteriores.
- El procesamiento masivo de extensiones quedó limitado a préstamos activos.
- Al aprobar una extensión se actualiza también la fecha vigente `equipo_prestamo.fecha_debe_devolver`, utilizada por el historial y los cálculos de atraso.
- Los ocho archivos modificados pasaron validación sintáctica de PHP/JavaScript y revisión de diff.
- Se desplegaron y verificaron por SHA-256 los tres archivos administrativos afectados en staging.
- `/socios` todavía no existe en staging. Sus cinco archivos modificados quedaron versionados, pero no se desplegarán aisladamente hasta preparar el portal con conexión staging y correo saliente neutralizado.
- Producción no fue modificada.

### 2026-07-26 - Correccion del botón de extensión de equipos en staging

- Se detectó que el listado mostraba las fechas desde un préstamo activo, pero buscaba extensiones pendientes en cualquier préstamo histórico del mismo equipo.
- Se creó la rama `fix/equipo-extension-active-loan` y el commit `d26fb02`.
- El listado ahora obtiene fechas, estados y token desde el mismo préstamo activo más reciente.
- Se desplegó únicamente `admin/components/equipo/models/equipo_list_procesa.php` en staging mediante FTPS y se verificó su integridad por SHA-256.
- Producción no fue modificada. Queda pendiente la prueba funcional del listado en staging.

### 2026-07-26 - Infraestructura inicial de staging

- Se creo `staging.ramuch.cl` con raiz documental independiente en `/staging.ramuch.cl`, fuera de `/public_html`.
- Se verifico que el subdominio responde correctamente mediante HTTPS.
- Se creo la base separada `ramuchcl_staging`.
- Se creo el usuario MySQL exclusivo `ramuchcl_staging_usr_nmi`; la contraseña no se registro en el proyecto.
- La proteccion HTTP del directorio fue postergada por decision del usuario. No se deben publicar datos reales mientras staging permanezca abierto.
- Todavia no se copio codigo, no se importo la base y no se modifico produccion.
- Posteriormente se genero un respaldo SQL comprimido de produccion y se importo correctamente en `ramuchcl_staging`.
- La importacion creo 30 tablas sin errores. El codigo de la aplicacion aun no fue copiado ni conectado.
- La base debe anonimizarse y neutralizar servicios externos antes de publicar el proyecto en staging.
- Se habilito proteccion HTTP del directorio, se copio `admin/` y se cambio su conexion a `ramuchcl_staging`.
- Se verifico desde la aplicacion que la conexion activa corresponde a staging y se elimino el diagnostico temporal.
- Se agrego una identificacion visual roja de ambiente de pruebas en la plantilla de staging.
- La prueba del listado de deudas reprodujo `DataTables: Invalid JSON response` y confirmo saltos de linea literales dentro de cadenas JSON.
- Se creo la rama `fix/deudas-ajax-json` y el commit `e2d154e`.
- El commit se publico en GitHub y sus cuatro archivos se desplegaron sólo en staging mediante FTPS.
- Los archivos remotos fueron descargados nuevamente y verificados por SHA-256.
- La prueba funcional confirmo que el listado carga y que desaparecieron los errores de JSON y de jQuery.
- Produccion no fue modificada y requiere confirmacion explicita para cualquier despliegue.

### 2026-07-22 - Diagnostico inicial del modulo deudas

- Se revisaron vistas, JavaScript, endpoints AJAX, modelos y logs de `admin/components/deudas/`.
- Todos los PHP y el JavaScript pasaron validacion sintactica.
- Se identifico como causa probable del error Ajax la construccion manual de JSON sin `json_encode()`.
- El log confirma un warning recurrente por el campo POST inexistente `medio`, nuevamente observado el 18-07-2026.
- Se documentaron riesgos adicionales de SQL, conteos, rendimiento, carga de archivos y manejo de errores en `DEUDAS_ANALYSIS.md`.
- No se modifico codigo funcional, base de datos ni produccion.

### 2026-07-22 - Comparacion exhaustiva consolidada

- Se compararon los 21.777 archivos rastreados por Git contra 15.168 archivos del snapshot.
- La union contiene 22.480 rutas: 9.420 identicas, 4.997 equivalentes salvo CRLF/LF, 7.312 sólo locales, 703 sólo en produccion y 48 con contenido realmente diferente.
- Los 5.664 archivos de codigo presentes en ambos lados son funcionalmente equivalentes.
- No se encontraron diferencias reales en codigo funcional propio compartido.
- Las 48 diferencias reales corresponden a datos operacionales, logs o WordPress protegido.
- Se genero un detalle de 13.060 diferencias con tamaños y hashes dentro del snapshot ignorado.
- Se agrego `scripts/compare-production.py` para repetir el analisis sin exponer contenido sensible.
- Se aplico el filtro definitivo excluyendo imagenes, logs, `.xls`, `.xlsx`, `.txt` y WordPress protegido: no queda ninguna diferencia real de codigo compartido.
- No se realizo staging, commit, despliegue ni modificacion de produccion.

### 2026-07-22 - Verificacion completa de socios

- El snapshot completo alcanzo aproximadamente 1,1 GiB y 15.169 archivos.
- La fotografia de `socios/` alcanzo 5.403 archivos y aproximadamente 249 MiB.
- Las 5.403 rutas coinciden exactamente con el clon local; no hay archivos exclusivos en ninguno de los dos lados.
- Se encontraron 3.060 archivos binariamente identicos y 2.343 diferentes.
- De las diferencias, 2.340 son únicamente conversiones CRLF/LF y tres corresponden a logs.
- No se encontraron diferencias funcionales de codigo en `socios/`.
- La fotografia de produccion queda suficientemente completa para continuar la clasificacion de los modulos propios, manteniendo WordPress y `registro2/` fuera del alcance.
- No se realizo staging, commit, despliegue ni modificacion de produccion.

### 2026-07-22 - Segundo inventario de produccion

- El snapshot crecio a aproximadamente 827 MiB y 10.197 archivos.
- `admin/` quedo completo con 7.670 archivos; `socios/` sigue incompleto y sólo contiene `template/`.
- En `admin/` se detectaron 139 archivos exclusivos de produccion: 138 imagenes o documentos operacionales y un PHP dentro de `pruebas/`.
- Se comprobo que las diferencias de PHP, JavaScript, CSS, HTML, JSON y SCSS compartidos corresponden a finales de linea CRLF/LF introducidos por la transferencia FTP.
- No se encontraron diferencias funcionales en el codigo fuente compartido dentro del alcance descargado.
- Se mantiene `registro2/` fuera del proyecto y WordPress completamente fuera del alcance.
- No se realizo staging, commit, despliegue ni modificacion de produccion.

### 2026-07-20 - Primera copia y comparacion de produccion

- Se preparo `.production-snapshots/` como ruta aislada y excluida de Git.
- Se establecio una conexion FTPS explicita de solo lectura con el servidor de produccion.
- Se verifico que el certificado presentado corresponde a HostGator, aunque no incluye el alias `ftp.ramuch.cl`.
- Se recibio una copia parcial de aproximadamente 55 MiB y 2.881 archivos.
- El listado FTP anuncia carpetas WordPress que no estan en la descarga; por ello la copia no se considera completa.
- Se compararon rutas y contenido sin modificar ninguno de los dos arboles.
- Los resultados provisionales se documentaron en `PRODUCTION_COMPARISON.md`.
- No se realizo staging, commit, despliegue ni modificacion de produccion.
- Se declaro `registro2/` como copia temporal pendiente de retiro; no debe incorporarse al proyecto.
- Se excluyeron `wp-admin/`, `wp-content/` y `wp-includes/` de cualquier conciliacion o despliegue.
- Una segunda revision determino que las copias de `admin/` y `socios/` sólo contienen `template/` y deben completarse antes de decidir qué version conservar.

### 2026-07-19 - Levantamiento inicial

- Se reviso la estructura general, el estado de Git, los tamaños y los tipos de archivos sensibles.
- Se identificaron modulos propios, datos operacionales versionados, configuraciones sensibles, logs y copias historicas.
- Se creo `.gitignore` para prevenir nuevas incorporaciones accidentales.
- Se creo esta bitacora y se definieron reglas iniciales de versionado y despliegue.
- No se modifico codigo funcional, no se elimino ningun archivo y no se hizo staging, commit ni despliegue.

## Pendientes priorizados

- [ ] Confirmar arquitectura real de produccion, version de PHP y dependencias habilitadas.
- [ ] Obtener un inventario de base de datos sin datos personales: motor, version, tamaño, tablas y relaciones.
- [ ] Clasificar cada ruta actualmente rastreada como codigo, dependencia, dato, secreto o archivo generado.
- [ ] Preparar la salida segura de secretos del historial y rotar credenciales.
- [ ] Definir una politica de respaldo y retencion para base de datos y archivos subidos.
- [ ] Crear entorno de desarrollo/staging y pruebas minimas de humo.
- [ ] Auditar autenticacion MD5 y diseñar su migracion.
- [ ] Diseñar y probar el despliegue automatizado con rollback antes de habilitar produccion.
