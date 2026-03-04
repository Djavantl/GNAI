# --- ESTÁGIO 1: Construção (Build) ---
FROM php:8.4-fpm-alpine AS builder

RUN apk add --no-cache \
    $PHPIZE_DEPS \
    libxml2-dev libpng-dev libjpeg-turbo-dev \
    freetype-dev libwebp-dev libzip-dev icu-dev zlib-dev

RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install gd zip pcntl pdo pdo_mysql opcache intl \
    && pecl install xlswriter pcov \
    && docker-php-ext-enable xlswriter pcov

# --- ESTÁGIO 2: Imagem Final (Runtime) ---
FROM php:8.4-fpm-alpine

ARG USER_ID=1000
ARG GROUP_ID=1000

WORKDIR /var/www

RUN apk add --no-cache \
    curl git unzip shadow \
    libxml2 libpng libjpeg-turbo freetype libwebp libzip icu-libs zlib \
    mysql-client

COPY --from=builder /usr/local/lib/php/extensions /usr/local/lib/php/extensions
COPY --from=builder /usr/local/etc/php/conf.d /usr/local/etc/php/conf.d
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN usermod -u ${USER_ID} www-data && groupmod -g ${GROUP_ID} www-data \
    && git config --global --add safe.directory /var/www

COPY composer.json composer.lock ./

RUN composer install --no-scripts --no-autoloader --prefer-dist

COPY . .

RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache \
    && composer dump-autoload --optimize --no-scripts

USER www-data

EXPOSE 9000

CMD ["sh", "-c", "php artisan storage:link --force && php artisan config:clear && php-fpm"]
