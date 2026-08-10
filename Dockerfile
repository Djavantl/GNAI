# syntax=docker/dockerfile:1.7

FROM php:8.3-fpm-alpine@sha256:9fcec48321d890240d700ccdc2b475420c87d398826e68c3d8830b8fca663e5c AS php_builder

RUN apk add --no-cache \
    $PHPIZE_DEPS \
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

COPY --from=composer:2@sha256:5946476338742b200bb9ff88f8be56275ddae4b3949c72305cb0dbf10cfcb760 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY composer.json composer.lock ./

RUN composer install \
    --prefer-dist \
    --no-dev \
    --no-interaction \
    --no-scripts \
    --no-autoloader \
    --no-progress

FROM node:20-alpine@sha256:fb4cd12c85ee03686f6af5362a0b0d56d50c58a04632e6c0fb8363f609372293 AS node_builder

WORKDIR /var/www

COPY package.json package-lock.json ./

RUN npm ci --no-audit --no-fund

COPY resources ./resources
COPY public ./public
COPY vite.config.js ./

RUN npm run build

FROM php:8.3-fpm-alpine@sha256:9fcec48321d890240d700ccdc2b475420c87d398826e68c3d8830b8fca663e5c

ARG USER_ID=1000
ARG GROUP_ID=1000

WORKDIR /var/www

RUN apk add --no-cache \
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

RUN usermod -u "${USER_ID}" www-data \
    && groupmod -g "${GROUP_ID}" www-data

COPY --from=php_builder /var/www/vendor ./vendor
COPY . .
COPY --from=node_builder /var/www/public/build ./public/build

RUN chmod +x /var/www/docker/php/entrypoint.sh \
    && mkdir -p storage/app/private/GNAIbackups \
    && mkdir -p storage/app/backup-temp \
    && chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache \
    && composer dump-autoload --optimize --no-dev --no-scripts \
    && rm -rf /tmp/* /var/cache/apk/*

USER www-data

EXPOSE 9000

CMD ["php-fpm"]
