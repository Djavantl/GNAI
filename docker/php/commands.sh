#!/bin/sh
# Biblioteca de comandos reutilizável
# Usado pelo entrypoint.sh e pode ser chamado via Makefile

set -e

# ==============================
# Funções
# ==============================

cmd_composer() {
    if [ ! -f "vendor/autoload.php" ]; then
        echo "📦 Instalando dependências PHP..."
        composer install --no-scripts --prefer-dist --no-interaction
        composer dump-autoload --optimize --no-scripts
        echo "✅ Composer OK"
    else
        echo "✅ vendor/ já existe"
    fi
}

cmd_permissions() {
    echo "🔐 Ajustando permissões..."
    chmod -R 775 storage bootstrap/cache 2>/dev/null || true
    echo "✅ Permissões OK"
}

cmd_storage_link() {
    echo "🔗 Verificando storage link..."
    if [ ! -L "public/storage" ]; then
        php artisan storage:link --force
        echo "✅ Storage link criado"
    else
        echo "✅ Storage link já existe"
    fi
}

cmd_cache_dev() {
    echo "🧹 Limpando cache (dev)..."
    php artisan optimize:clear
    echo "✅ Cache limpo"
}

cmd_cache_prod() {
    echo "⚡ Gerando cache de produção..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    echo "✅ Cache gerado"
}

cmd_migrate() {
    echo "🗄️  Rodando migrations..."
    php artisan migrate --force
    echo "✅ Migrations OK"
}

cmd_seed() {
    echo "🌱 Rodando seeds..."
    php artisan db:seed --force
    echo "✅ Seeds OK"
}

cmd_npm_build() {
    echo "🏗️  Rodando npm build..."
    npm install --prefix /var/www
    npm run build --prefix /var/www
    echo "✅ NPM build OK"
}

cmd_queue() {
    echo "⚙️  Iniciando queue worker..."
    php artisan queue:work --sleep=3 --tries=3 --max-time=3600
}

cmd_scheduler() {
    echo "🕐 Iniciando scheduler..."
    php artisan schedule:work
}

# ==============================
# Execução direta via argumento
# Permite: sh commands.sh migrate
# ==============================
if [ "$(basename -- "$0")" = "commands.sh" ]; then
    if [ -n "$1" ]; then
        case "$1" in
            composer)       cmd_composer ;;
            permissions)    cmd_permissions ;;
            storage-link)   cmd_storage_link ;;
            cache-dev)      cmd_cache_dev ;;
            cache-prod)     cmd_cache_prod ;;
            migrate)        cmd_migrate ;;
            seed)           cmd_seed ;;
            npm-build)      cmd_npm_build ;;
            queue)          cmd_queue ;;
            scheduler)      cmd_scheduler ;;
            *)              echo "❌ Comando desconhecido: $1" && exit 1 ;;
        esac
    fi
fi
