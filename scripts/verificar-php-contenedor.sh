#!/usr/bin/env bash
set -Eeuo pipefail

# Version segura y superior a la rama PHP 8.2 usada actualmente en hosting.
# Puede reemplazarse por otra imagen oficial corregida, por ejemplo:
# PHP_IMAGE=php:8.5.12-cli ./scripts/verificar-php-contenedor.sh
PHP_IMAGE="${PHP_IMAGE:-php:8.4.27-cli}"

command -v docker >/dev/null 2>&1 || {
    echo "ERROR: Docker es obligatorio para verificar PHP." >&2
    exit 1
}

project_root="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")/.." && pwd)"

echo "Verificando con el contenedor $PHP_IMAGE"

docker run --rm \
    --volume "$project_root:/app:ro" \
    --workdir /app \
    "$PHP_IMAGE" \
    sh -ceu '
        current_version="$(php -r "echo PHP_VERSION;")"
        current_branch="$(php -r "echo PHP_MAJOR_VERSION, chr(46), PHP_MINOR_VERSION;")"

        case "$current_branch" in
            8.2) minimum_safe="8.2.35" ;;
            8.3) minimum_safe="8.3.36" ;;
            8.4) minimum_safe="8.4.27" ;;
            8.5) minimum_safe="8.5.12" ;;
            *)
                echo "ERROR: PHP $current_version no pertenece a una rama estable aprobada." >&2
                exit 1
                ;;
        esac

        php -r '"'"'
            if (version_compare(PHP_VERSION, $argv[1], "<")) {
                fwrite(STDERR, "ERROR: PHP " . PHP_VERSION . " es vulnerable; se requiere " . $argv[1] . " o superior en esta rama.\n");
                exit(1);
            }
        '"'"' "$minimum_safe"

        echo "PHP $current_version cumple el mínimo de seguridad $minimum_safe."

        find . -type f -name "*.php" \
            -not -path "./.git/*" \
            -not -path "*/node_modules/*" \
            -print0 \
        | sort -z \
        | xargs -0 -r -n 1 php -l >/tmp/php-lint.log

        checked="$(wc -l </tmp/php-lint.log)"
        echo "Sintaxis PHP válida en $checked archivos."
    '
