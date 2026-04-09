#!/bin/sh
set -e

. /usr/local/bin/commands.sh

echo "🚀 Iniciando GNAI..."

cmd_composer
cmd_permissions
cmd_storage_link

[ "$RUN_MIGRATIONS" = "true" ] && cmd_migrate
[ "$RUN_SEEDS"      = "true" ] && cmd_seed

if [ "$APP_ENV" = "production" ]; then
    cmd_cache_prod
else
    cmd_cache_dev || echo "⚠️ Cache não pôde ser limpo (provavelmente tabelas ausentes). Continuando..."
fi

[ "$RUN_NPM_BUILD"  = "true" ] && cmd_npm_build

echo "✅ Boot finalizado, iniciando PHP-FPM..."
exec "$@"
