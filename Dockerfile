FROM php:8.4-fpm-alpine

ARG USER_ID=1000
ARG GROUP_ID=1000

# 1. Instalação de dependências do sistema e PHP
RUN apk update && apk add --no-cache \
    curl git unzip build-base shadow \
    libxml2-dev libpng-dev libjpeg-turbo-dev \
    freetype-dev libwebp-dev libzip-dev zip \
    icu-dev zlib-dev mysql-client \
    $PHPIZE_DEPS \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install \
        gd zip pcntl pdo pdo_mysql opcache intl \
    && pecl install xlswriter \
    && docker-php-ext-enable xlswriter \
    && rm -rf /var/cache/apk/*

# 2. Configuração de Usuário, Composer e Git
RUN usermod -u ${USER_ID} www-data && groupmod -g ${GROUP_ID} www-data
# Resolve o erro de "dubious ownership" do Git
RUN git config --global --add safe.directory /var/www

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# 3. Otimização de Cache: Dependências primeiro
COPY composer.json composer.lock ./
RUN COMPOSER_MEMORY_LIMIT=-1 composer install --prefer-dist --no-interaction --no-scripts --no-autoloader

# 4. Copia o código e ajusta permissões
COPY . .
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# 5. Finaliza Composer (Gera autoload sem rodar scripts que dependem de banco)
RUN composer dump-autoload --optimize --no-scripts

EXPOSE 9000

# Comando Principal: Aqui os scripts rodam com acesso ao banco de dados
CMD ["sh", "-c", " \
    php artisan storage:link --force && \
    php artisan config:clear && \
    php artisan route:clear && \
    php artisan view:clear && \
    chown -R www-data:www-data /var/www/storage && \
    php-fpm"]
