#!/usr/bin/env bash
set -Eeuo pipefail

# Respaldo lógico completo de MySQL/MariaDB.
#
# Variables obligatorias:
#   DB_HOST               Host MySQL.
#   DB_USER               Usuario MySQL con permisos de lectura, vistas,
#                         triggers, rutinas y eventos.
#   DB_NAME               Base de datos.
#   DB_PASSWORD_FILE      Archivo legible que contiene solamente la contraseña.
#   BACKUP_DIR            Directorio absoluto, fuera del sitio web.
#
# Cifrado (recomendado y obligatorio por defecto):
#   BACKUP_ENCRYPTION_KEY_FILE  Archivo que contiene la clave de cifrado.
#
# Para permitir excepcionalmente un respaldo sin cifrar:
#   BACKUP_ALLOW_UNENCRYPTED=YES
#
# Variables opcionales:
#   DB_PORT=3306
#   BACKUP_PREFIX=ramuch
#
# Ejemplo:
#   DB_HOST=servidor DB_USER=usuario DB_NAME=base \
#   DB_PASSWORD_FILE=/ruta/segura/mysql.pass \
#   BACKUP_ENCRYPTION_KEY_FILE=/ruta/segura/backup.key \
#   BACKUP_DIR=/ruta/fuera/de/public_html \
#   ./scripts/backup-database.sh

fail() {
    echo "ERROR: $*" >&2
    exit 1
}

require_variable() {
    local variable_name="$1"
    [[ -n "${!variable_name:-}" ]] || fail "Falta la variable $variable_name"
}

require_variable DB_HOST
require_variable DB_USER
require_variable DB_NAME
require_variable DB_PASSWORD_FILE
require_variable BACKUP_DIR

DB_PORT="${DB_PORT:-3306}"
BACKUP_PREFIX="${BACKUP_PREFIX:-ramuch}"

[[ "$DB_PORT" =~ ^[0-9]+$ ]] || fail "DB_PORT debe ser numérico"
[[ "$BACKUP_DIR" = /* ]] || fail "BACKUP_DIR debe ser una ruta absoluta"
[[ -r "$DB_PASSWORD_FILE" ]] || fail "No se puede leer DB_PASSWORD_FILE"

if [[ -z "${BACKUP_ENCRYPTION_KEY_FILE:-}" ]]; then
    [[ "${BACKUP_ALLOW_UNENCRYPTED:-}" == "YES" ]] || \
        fail "Defina BACKUP_ENCRYPTION_KEY_FILE o confirme BACKUP_ALLOW_UNENCRYPTED=YES"
else
    [[ -r "$BACKUP_ENCRYPTION_KEY_FILE" ]] || \
        fail "No se puede leer BACKUP_ENCRYPTION_KEY_FILE"
    command -v openssl >/dev/null || fail "No está instalado openssl"
fi

if command -v mariadb-dump >/dev/null; then
    DUMP_COMMAND=(mariadb-dump)
    DUMP_FLAVOR="mariadb"
elif command -v mysqldump >/dev/null; then
    DUMP_COMMAND=(mysqldump)
    DUMP_FLAVOR="mysql"
elif command -v docker >/dev/null; then
    DUMP_COMMAND=(docker run --rm -i -e MYSQL_PWD mariadb:10.11 mariadb-dump)
    DUMP_FLAVOR="mariadb"
else
    fail "No está instalado mariadb-dump, mysqldump ni Docker"
fi

command -v gzip >/dev/null || fail "No está instalado gzip"
command -v sha256sum >/dev/null || fail "No está instalado sha256sum"
command -v realpath >/dev/null || fail "No está instalado realpath"

umask 077
BACKUP_DIR="$(realpath -m -- "$BACKUP_DIR")"
project_root="$(realpath -m -- "$(dirname -- "${BASH_SOURCE[0]}")/..")"

case "$BACKUP_DIR/" in
    "$project_root/"*) fail "BACKUP_DIR debe estar fuera del proyecto/sitio web" ;;
esac
case "$BACKUP_DIR/" in
    */public_html/*) fail "BACKUP_DIR no puede estar dentro de public_html" ;;
esac

mkdir -p -- "$BACKUP_DIR"
[[ -d "$BACKUP_DIR" && -w "$BACKUP_DIR" ]] || \
    fail "BACKUP_DIR no existe o no permite escritura"

timestamp="$(date '+%Y%m%d_%H%M%S')"
safe_database="${DB_NAME//[^a-zA-Z0-9_.-]/_}"
base_name="${BACKUP_PREFIX}_${safe_database}_${timestamp}"
temporary_sql="$(mktemp "$BACKUP_DIR/.${base_name}.XXXXXX.sql")"
temporary_gzip="${temporary_sql}.gz"

cleanup() {
    rm -f -- "$temporary_sql" "$temporary_gzip"
    unset MYSQL_PWD || true
}
trap cleanup EXIT

MYSQL_PWD="$(<"$DB_PASSWORD_FILE")"
export MYSQL_PWD

echo "Iniciando respaldo completo de $DB_NAME..."

dump_arguments=(
    --host="$DB_HOST"
    --port="$DB_PORT"
    --user="$DB_USER"
    --protocol=TCP
    --single-transaction
    --quick
    --skip-lock-tables
    --routines
    --triggers
    --events
    --hex-blob
    --default-character-set=utf8mb4
    --no-tablespaces
    --add-drop-table
    --add-drop-trigger
    --databases "$DB_NAME"
)

if [[ "$DUMP_FLAVOR" == "mysql" ]]; then
    dump_arguments+=(--set-gtid-purged=OFF)
fi

"${DUMP_COMMAND[@]}" "${dump_arguments[@]}" > "$temporary_sql"
unset MYSQL_PWD

[[ -s "$temporary_sql" ]] || fail "El volcado quedó vacío"
grep -Eq 'CREATE TABLE|CREATE VIEW' "$temporary_sql" || \
    fail "El volcado no contiene definiciones de tablas o vistas"

gzip -9 -- "$temporary_sql"
gzip -t -- "$temporary_gzip"

if [[ -n "${BACKUP_ENCRYPTION_KEY_FILE:-}" ]]; then
    final_backup="$BACKUP_DIR/${base_name}.sql.gz.enc"
    openssl enc -aes-256-cbc -salt -pbkdf2 -iter 200000 \
        -pass "file:$BACKUP_ENCRYPTION_KEY_FILE" \
        -in "$temporary_gzip" \
        -out "$final_backup"

    openssl enc -d -aes-256-cbc -pbkdf2 -iter 200000 \
        -pass "file:$BACKUP_ENCRYPTION_KEY_FILE" \
        -in "$final_backup" | gzip -t
else
    final_backup="$BACKUP_DIR/${base_name}.sql.gz"
    mv -- "$temporary_gzip" "$final_backup"
fi

checksum_file="${final_backup}.sha256"
(
    cd -- "$(dirname -- "$final_backup")"
    sha256sum -- "$(basename -- "$final_backup")" > "$(basename -- "$checksum_file")"
    sha256sum --check "$(basename -- "$checksum_file")"
)

backup_size="$(du -h -- "$final_backup" | awk '{print $1}')"
echo "Respaldo completado y verificado."
echo "Archivo: $final_backup"
echo "Checksum: $checksum_file"
echo "Tamaño: $backup_size"

if [[ -n "${BACKUP_ENCRYPTION_KEY_FILE:-}" ]]; then
    echo "Restauración: descifrar, descomprimir y enviar al cliente mysql/mariadb."
    echo "La clave de cifrado debe conservarse separada del respaldo."
fi
