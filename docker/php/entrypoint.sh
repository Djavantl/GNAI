#!/bin/sh
set -e

ROLE="${1:-app}"
COMPOSER_STATE_FILE="storage/framework/cache/.docker-composer-state"
ARTISAN_STATE_FILE="storage/framework/cache/.docker-artisan-state"

cd /var/www

mkdir -p \
    bootstrap/cache \
    storage/app/backup-temp \
    storage/app/private/GNAIbackups \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs

chmod -R ug+rwX bootstrap/cache storage || true

if [ ! -f .env ] && [ -f .env.example ]; then
    cp .env.example .env
fi

composer_state() {
    LOCK_HASH="$(md5sum composer.lock 2>/dev/null | awk '{print $1}')"
    PHP_VERSION_KEY="$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')"
    printf '%s' "${LOCK_HASH}-php${PHP_VERSION_KEY}"
}

current_state="$(composer_state)"

wait_for_database() {
    if [ "${DB_CONNECTION:-}" != "mysql" ] || [ -z "${DB_HOST:-}" ]; then
        return 0
    fi

    echo "Aguardando banco de dados em ${DB_HOST}:${DB_PORT:-3306}..."

    until php -r '
        $host = getenv("DB_HOST") ?: "db";
        $port = getenv("DB_PORT") ?: "3306";
        $database = getenv("DB_DATABASE") ?: "";
        $username = getenv("DB_USERNAME") ?: "root";
        $password = getenv("DB_PASSWORD") ?: "";

        try {
            new PDO(
                "mysql:host={$host};port={$port};dbname={$database}",
                $username,
                $password,
                [PDO::ATTR_TIMEOUT => 3]
            );
            exit(0);
        } catch (Throwable $e) {
            fwrite(STDERR, "Banco ainda indisponivel: ".$e->getMessage().PHP_EOL);
            exit(1);
        }
    '; do
        sleep 2
    done
}

ensure_php_dependencies() {
    stored_state=""
    composer_install_flags="--prefer-dist --no-interaction --no-progress --no-scripts"

    if [ -f "${COMPOSER_STATE_FILE}" ]; then
        stored_state="$(cat "${COMPOSER_STATE_FILE}")"
    fi

    if [ "${APP_ENV:-local}" = "production" ]; then
        if [ ! -f vendor/autoload.php ]; then
            echo "Dependencias PHP ausentes na imagem de producao: vendor/autoload.php nao encontrado." >&2
            exit 1
        fi

        if [ "${stored_state}" != "${current_state}" ]; then
            printf '%s' "${current_state}" > "${COMPOSER_STATE_FILE}"
            rm -f "${ARTISAN_STATE_FILE}"
        fi

        return 0
    fi

    if [ ! -f vendor/autoload.php ] || [ "${stored_state}" != "${current_state}" ]; then
        echo "Sincronizando dependencias PHP para ${current_state}..."
        composer install ${composer_install_flags}
        composer dump-autoload --optimize --no-scripts
        printf '%s' "${current_state}" > "${COMPOSER_STATE_FILE}"
        rm -f "${ARTISAN_STATE_FILE}"
    fi
}

wait_for_php_dependencies() {
    echo "Aguardando bootstrap PHP do container app..."

    until [ -f vendor/autoload.php ] \
        && [ -f "${COMPOSER_STATE_FILE}" ] \
        && [ "$(cat "${COMPOSER_STATE_FILE}")" = "${current_state}" ] \
        && [ -f "${ARTISAN_STATE_FILE}" ] \
        && [ "$(cat "${ARTISAN_STATE_FILE}")" = "${current_state}" ]; do
        sleep 2
    done
}

bootstrap_laravel() {
    stored_state=""

    if [ -f "${ARTISAN_STATE_FILE}" ]; then
        stored_state="$(cat "${ARTISAN_STATE_FILE}")"
    fi

    if [ "${stored_state}" != "${current_state}" ]; then
        echo "Executando bootstrap Laravel para ${current_state}..."
        php artisan package:discover --ansi
        printf '%s' "${current_state}" > "${ARTISAN_STATE_FILE}"
    fi
}

if [ "${ROLE}" = "app" ]; then
    ensure_php_dependencies
fi

wait_for_database

if [ "${ROLE}" = "app" ]; then
    bootstrap_laravel
else
    wait_for_php_dependencies
fi

if [ "${ROLE}" = "app" ]; then
    php artisan storage:link --force || true

    if [ "${APP_ENV:-local}" = "production" ]; then
        php artisan config:cache
        php artisan route:cache
        php artisan view:cache
    else
        php artisan migrate --force
    fi

    exec php-fpm
fi

if [ "${ROLE}" = "scheduler" ]; then
    exec php artisan schedule:work
fi

if [ "${ROLE}" = "queue" ]; then
    exec php artisan queue:work --sleep=3 --tries=3 --max-time=3600
fi

exec "$@"
