# ----------------------------------------------------
# FASE 1: CONSTRUÇÃO DA IMAGEM PHP/LARAVEL (PHP-FPM)
# ----------------------------------------------------

FROM php:8.4-fpm-alpine

ARG USER_ID=1000
ARG GROUP_ID=1000

# Instala ferramentas e extensões necessárias
RUN apk update && apk add --no-cache \
    curl \
    git \
    unzip \
    build-base \
    libxml2-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    shadow \
    freetype-dev \
    libzip-dev \
    zip \
    mysql-client \
    && docker-php-ext-install zip pcntl \
    && rm -rf /var/cache/apk/* \
    \
    && docker-php-ext-install pdo pdo_mysql opcache \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

# Sincroniza o usuário www-data do container com o seu usuário do computador
RUN usermod -u ${USER_ID} www-data && groupmod -g ${GROUP_ID} www-data

# Composer global
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Diretório de trabalho
WORKDIR /var/www

# Copia os arquivos do projeto
COPY . .

# Permissões iniciais para pastas de cache e storage
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Instala dependências do Laravel
RUN composer install --prefer-dist --no-interaction

# Limpa cache Laravel para evitar conflitos de configuração
RUN php artisan config:clear \
    && php artisan route:clear \
    && php artisan view:clear

# Expõe a porta FPM
EXPOSE 9000

# Comando principal: Garante o link do storage e as permissões toda vez que o container subir
CMD ["sh", "-c", "php artisan storage:link --force && chown -R www-data:www-data /var/www/storage && php-fpm"]
