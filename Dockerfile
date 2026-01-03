# ----------------------------------------------------
# FASE 1: CONSTRUÇÃO DA IMAGEM PHP/LARAVEL (PHP-FPM)
# ----------------------------------------------------

FROM php:8.4-fpm-alpine

# Instala ferramentas e extensões necessárias
RUN apk update && apk add --no-cache \
    curl \
    git \
    unzip \
    build-base \
    libxml2-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    && rm -rf /var/cache/apk/* \
    \
    && docker-php-ext-install pdo pdo_mysql opcache \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

# Composer global
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Diretório de trabalho
WORKDIR /var/www

# Copia os arquivos do projeto
COPY . .

# Permissões
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Instala dependências do Laravel
RUN composer install --no-scripts --prefer-dist

# Limpa cache Laravel
RUN php artisan config:clear \
    && php artisan route:clear \
    && php artisan view:clear

# Expõe a porta FPM
EXPOSE 9000

# Comando principal
CMD ["php-fpm"]
