#!/bin/sh
set -eu

cd /var/www

log() {
  printf '[entrypoint] %s\n' "$*"
}

: "${INSTALL_COMPOSER_DEPS:=true}"
: "${INSTALL_NPM_DEPS:=true}"
: "${RUN_MIGRATIONS:=true}"

# Ambiente de desenvolvimento: garante diretorios e instala deps se faltarem.
log "Criando diretorios necessarios do Laravel..."
mkdir -p /var/www/storage/framework/cache/data \
         /var/www/storage/framework/sessions \
         /var/www/storage/framework/views \
         /var/www/storage/logs \
         /var/www/bootstrap/cache
chmod -R a+rwX /var/www/storage /var/www/bootstrap/cache || true

if [ "$INSTALL_COMPOSER_DEPS" = "true" ] && [ -f composer.json ] && [ ! -f vendor/autoload.php ]; then
  log "Instalando dependencias do Composer..."
  COMPOSER_CACHE_DIR=/tmp/composer-cache composer install --no-interaction
fi

if [ "$INSTALL_NPM_DEPS" = "true" ] && [ -f package.json ] && [ ! -d node_modules ]; then
  log "Instalando dependencias do npm..."
  npm install
fi

if [ "$RUN_MIGRATIONS" = "true" ]; then
  log "Rodando migrations..."
  php artisan migrate --force
fi

log "Iniciando processo principal: $*"
exec "$@"
