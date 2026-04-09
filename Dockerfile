# --- ESTÁGIO 1: Construção (Build) ---
FROM php:8.2-fpm-alpine AS builder

RUN apk add --no-cache \
    $PHPIZE_DEPS \
    libxml2-dev libpng-dev libjpeg-turbo-dev \
    freetype-dev libwebp-dev libzip-dev icu-dev zlib-dev

# Extensões PHP + phpredis
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install gd zip pcntl pdo pdo_mysql opcache intl \
    && pecl install redis \
    && docker-php-ext-enable redis

# --- ESTÁGIO 2: Imagem Final (Runtime) ---
FROM php:8.2-fpm-alpine

ARG USER_ID=1000
ARG GROUP_ID=1000

WORKDIR /var/www

RUN apk add --no-cache \
    curl git unzip shadow \
    libxml2 libpng libjpeg-turbo freetype libwebp libzip icu-libs zlib \
    mysql-client \
    tzdata \
    && cp /usr/share/zoneinfo/America/Sao_Paulo /etc/localtime \
    && echo "America/Sao_Paulo" > /etc/timezone \
    && apk del tzdata

COPY --from=builder /usr/local/lib/php/extensions /usr/local/lib/php/extensions
COPY --from=builder /usr/local/etc/php/conf.d /usr/local/etc/php/conf.d
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN usermod -u ${USER_ID} www-data \
    && groupmod -g ${GROUP_ID} www-data \
    && git config --global --add safe.directory /var/www

COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-autoloader --prefer-dist --no-interaction

COPY . .

RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache \
    && composer dump-autoload --optimize --no-scripts

USER www-data

EXPOSE 9000

CMD ["php-fpm"]
