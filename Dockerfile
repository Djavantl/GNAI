# syntax=docker/dockerfile:1.7

FROM php:8.3-fpm-alpine AS php_builder

RUN apk add --no-cache \
    $PHPIZE_DEPS \
    git \
    unzip \
    icu-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libwebp-dev \
    libxml2-dev \
    libzip-dev \
    freetype-dev \
    oniguruma-dev \
    sqlite-dev \
    zlib-dev

RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j"$(nproc)" \
        bcmath \
        exif \
        gd \
        intl \
        mbstring \
        opcache \
        pcntl \
        pdo_mysql \
        pdo_sqlite \
        xml \
        zip

RUN git clone --depth 1 --branch 6.3.0 https://github.com/phpredis/phpredis.git /tmp/phpredis \
    && cd /tmp/phpredis \
    && phpize \
    && ./configure \
    && make -j"$(nproc)" \
    && make install \
    && docker-php-ext-enable redis \
    && rm -rf /tmp/phpredis

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY composer.json composer.lock ./

RUN composer install \
    --prefer-dist \
    --no-interaction \
    --no-scripts \
    --no-autoloader

FROM node:20-alpine AS node_builder

WORKDIR /var/www

COPY package.json package-lock.json ./

RUN npm ci

COPY resources ./resources
COPY public ./public
COPY vite.config.js ./

RUN npm run build

FROM php:8.3-fpm-alpine

ARG USER_ID=1000
ARG GROUP_ID=1000

WORKDIR /var/www

RUN apk add --no-cache \
    bash \
    curl \
    git \
    icu-libs \
    libjpeg-turbo \
    libpng \
    libwebp \
    libxml2 \
    libzip \
    freetype \
    mysql-client \
    oniguruma \
    shadow \
    sqlite-libs \
    tzdata \
    unzip \
    zlib \
    && cp /usr/share/zoneinfo/America/Bahia /etc/localtime \
    && echo "America/Bahia" > /etc/timezone \
    && apk del tzdata

COPY --from=php_builder /usr/local/lib/php/extensions /usr/local/lib/php/extensions
COPY --from=php_builder /usr/local/etc/php/conf.d /usr/local/etc/php/conf.d
COPY --from=php_builder /usr/bin/composer /usr/bin/composer

RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS linux-headers \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && apk del .build-deps

RUN usermod -u "${USER_ID}" www-data \
    && groupmod -g "${GROUP_ID}" www-data \
    && git config --global --add safe.directory /var/www

COPY --from=php_builder /var/www/vendor ./vendor
COPY . .
RUN rm -f bootstrap/cache/config.php bootstrap/cache/routes-v7.php
COPY --from=node_builder /var/www/public/build ./public/build

RUN chmod +x /var/www/docker/php/entrypoint.sh \
    && mkdir -p storage/app/private/GNAIbackups \
    && mkdir -p storage/app/backup-temp \
    && chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache \
    && composer dump-autoload --optimize --no-scripts

USER www-data

EXPOSE 9000

CMD ["php-fpm"]
