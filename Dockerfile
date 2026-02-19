# --- ESTÁGIO 1: Construção (Build) ---
FROM php:8.4-fpm-alpine AS builder

RUN apk add --no-cache \
    $PHPIZE_DEPS \
    libxml2-dev libpng-dev libjpeg-turbo-dev \
    freetype-dev libwebp-dev libzip-dev icu-dev zlib-dev

RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install gd zip pcntl pdo pdo_mysql opcache intl \
    && pecl install xlswriter \
    && docker-php-ext-enable xlswriter

# --- ESTÁGIO 2: Imagem Final (Runtime) ---
FROM php:8.4-fpm-alpine

ARG USER_ID=1000
ARG GROUP_ID=1000

WORKDIR /var/www

# Instalando apenas o necessário para execução
# icu-libs é o correto para produção
RUN apk add --no-cache \
    curl git unzip shadow \
    libxml2 libpng libjpeg-turbo freetype libwebp libzip icu-libs zlib \
    mysql-client

# Copia extensões e Composer
COPY --from=builder /usr/local/lib/php/extensions /usr/local/lib/php/extensions
COPY --from=builder /usr/local/etc/php/conf.d /usr/local/etc/php/conf.d
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configuração de usuário e Git (Segurança e Permissão)
RUN usermod -u ${USER_ID} www-data && groupmod -g ${GROUP_ID} www-data \
    && git config --global --add safe.directory /var/www

# --- MELHORIA DE CACHE: Primeiro as dependências ---
COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-autoloader --no-dev --prefer-dist

# Agora o código (Mudanças aqui não forçam o download do composer novamente)
COPY . .

# 2. Ajusta permissões E roda o dump sem scripts
# O --no-scripts impede que o Laravel tente conectar no DB agora
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache \
    && composer dump-autoload --optimize --no-dev --no-scripts

USER www-data

EXPOSE 9000

# O package:discover vai rodar automaticamente quando o container subir,
# já com o banco de dados disponível.
CMD ["sh", "-c", "php artisan storage:link --force && php artisan config:clear && php-fpm"]
