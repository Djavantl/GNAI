# GNAI

Sistema de gestao do Nucleo de Acessibilidade e Inclusao. O projeto concentra dois modulos no mesmo repositorio:

- `Atendimento Educacional Especializado (AEE)`
- `Radar Inclusivo`

O ambiente foi padronizado para subir via Docker com bootstrap automatico, `entrypoint` unico para os containers PHP e `docker-compose` separado para desenvolvimento e producao.

## Stack

- `Laravel 12`
- `PHP 8.2` via Docker
- `MySQL 8.0`
- `Redis 7`
- `Node 20 + Vite`
- `Nginx`
- `Docker Compose`

## Estrutura do ambiente

### Desenvolvimento

O arquivo [docker-compose.dev.yml](/home/marley/Projetos/GNAI/docker-compose.dev.yml:1) sobe:

- `app`: container principal do Laravel/PHP-FPM
- `scheduler`: roda o agendador do Laravel
- `db`: MySQL
- `redis`: Redis
- `nginx`: proxy reverso da aplicacao
- `node`: Vite em modo desenvolvimento
- `phpmyadmin`: opcional, via profile `tools`

Portas padrao em `dev`:

- aplicacao: `http://localhost:8080`
- Vite: `http://localhost:5173`
- MySQL exposto no host: `3307`
- PHP-FPM: `9000`
- phpMyAdmin: `http://localhost:8081` com `--profile tools`

### Producao

O arquivo [docker-compose.prod.yml](/home/marley/Projetos/GNAI/docker-compose.prod.yml:1) sobe:

- `app`
- `scheduler`
- `queue`
- `db`
- `redis`
- `nginx`

Diferencas principais da producao:

- usa `.env.prod`
- `nginx` exposto em `80` e `443`
- existe um container `queue`
- o `entrypoint` faz cache de config/rotas/views em vez de rodar migration automatica

## Como o Docker esta funcionando

### Dockerfile

O [Dockerfile](/home/marley/Projetos/GNAI/Dockerfile:1) e multi-stage:

1. `php_builder`
   - instala extensoes PHP necessarias
   - instala dependencias do Composer sem scripts
2. `node_builder`
   - instala dependencias frontend
   - gera `public/build`
3. imagem final PHP
   - copia extensoes, `composer`, `vendor` e assets compilados
   - ajusta usuario `www-data` para o UID/GID local
   - prepara diretorios de `storage`

### Imagem PHP compartilhada

Em `dev`, `app` e `scheduler` usam a mesma imagem:

- `gnai-php:dev`

Em `prod`, `app`, `scheduler` e `queue` usam:

- `gnai-php:prod`

Isso evita construir imagens PHP duplicadas para papeis diferentes.

### Entrypoint unico

O script [docker/php/entrypoint.sh](/home/marley/Projetos/GNAI/docker/php/entrypoint.sh:1) e o mesmo para os containers PHP. Ele recebe um papel:

- `app`
- `scheduler`
- `queue`

Fluxo do `entrypoint`:

1. cria diretorios criticos de `storage` e `bootstrap/cache`
2. copia `.env.example` para `.env` se nao houver arquivo
3. sincroniza `vendor` com `composer.lock` e com a versao atual do PHP
4. espera o banco responder via `PDO`
5. executa bootstrap do Laravel quando necessario

Comportamento por papel:

- `app`
  - garante dependencias PHP
  - roda `php artisan package:discover`
  - roda `php artisan storage:link --force`
  - em `APP_ENV=local`: roda `php artisan migrate --force`
  - em `APP_ENV=production`: roda `config:cache`, `route:cache` e `view:cache`
  - ao final inicia `php-fpm`
- `scheduler`
  - espera o bootstrap completo do container `app`
  - executa `php artisan schedule:work`
- `queue`
  - espera o bootstrap completo do container `app`
  - executa `php artisan queue:work --sleep=3 --tries=3 --max-time=3600`

## Variaveis de ambiente

Arquivos base:

- [\.env.example](/home/marley/Projetos/GNAI/.env.example:1)
- [\.env.dev.example](/home/marley/Projetos/GNAI/.env.dev.example:1)
- [\.env.prod.example](/home/marley/Projetos/GNAI/.env.prod.example:1)

Arquivos usados pelo Docker:

- `dev`: [\.env.dev](/home/marley/Projetos/GNAI/.env.dev:1)
- `prod`: [\.env.prod](/home/marley/Projetos/GNAI/.env.prod:1)

### Variaveis obrigatorias

Estas merecem atencao antes de subir o projeto:

- `APP_NAME`
  - nome da aplicacao
- `APP_ENV`
  - use `local` em desenvolvimento e `production` em producao
- `APP_KEY`
  - chave do Laravel; gere com `php artisan key:generate`
- `APP_DEBUG`
  - `true` em desenvolvimento, `false` em producao
- `APP_URL`
  - URL principal da aplicacao
- `DB_CONNECTION`
  - deve permanecer `mysql`
- `DB_HOST`
  - no Docker deve permanecer `db`
- `DB_PORT`
  - no Docker normalmente `3306`
- `DB_DATABASE`
  - nome do banco
- `DB_USERNAME`
  - usuario da aplicacao
- `DB_PASSWORD`
  - senha do usuario da aplicacao
- `REDIS_HOST`
  - no Docker deve ser `redis`

### Variaveis de localizacao e mapa

- `OSM_API_URL`
  - endpoint de geocodificacao
- `OSM_USER_AGENT`
  - identificacao obrigatoria para consumo do Nominatim/OpenStreetMap

Recomendacao:

- informe nome e e-mail reais do projeto/equipe em `OSM_USER_AGENT`

### Variaveis de backup

- `BACKUP_DISK_NAME`
  - nome do disco configurado para os backups
- `BACKUP_PATH`
  - caminho local onde os arquivos zip e dumps serao salvos
- `BACKUP_MYSQL_BINARY_PATH`
  - caminho do cliente MySQL dentro do container
- `BACKUP_MYSQL_EXTRA_OPTIONS`
  - opcoes extras de dump; hoje o projeto usa `--protocol=tcp --skip-ssl`
- `BACKUP_ARCHIVE_PASSWORD`
  - senha opcional do arquivo compactado
- `BACKUP_MAIL_TO`
  - destinatario de notificacoes de backup

### Variaveis de sessao, cache e fila

- `SESSION_DRIVER`
  - recomendado `database`
- `CACHE_STORE`
  - recomendado `database`
- `QUEUE_CONNECTION`
  - recomendado `database`
- `FILESYSTEM_DISK`
  - recomendado `public`
- `BROADCAST_CONNECTION`
  - pode permanecer `log` se nao houver broadcast em tempo real

### Variaveis de Redis

- `REDIS_CLIENT`
  - use `phpredis`
- `REDIS_HOST`
  - `redis` no Docker
- `REDIS_PORT`
  - `6379`
- `REDIS_DB`
  - base principal
- `REDIS_CACHE_DB`
  - base usada para cache

### Variaveis de e-mail

Desenvolvimento:

- pode usar `MAIL_MAILER=log`

Producao:

- configure `MAIL_MAILER=smtp`
- preencha `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_ENCRYPTION`
- ajuste `MAIL_FROM_ADDRESS` e `MAIL_FROM_NAME`

## Como preparar os arquivos `.env`

### Desenvolvimento

1. Crie o arquivo:

```bash
cp .env.dev.example .env.dev
```

2. Ajuste pelo menos:

```env
APP_NAME=GNAI
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8080

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=gnai_db
DB_USERNAME=gnai_user
DB_PASSWORD=sua_senha

REDIS_HOST=redis
REDIS_PORT=6379

OSM_USER_AGENT="SeuNome - seuemail@dominio.com"
```

3. Depois de subir o ambiente, gere a chave:

```bash
make art key:generate
```

### Producao

1. Crie o arquivo:

```bash
cp .env.prod.example .env.prod
```

2. Ajuste obrigatoriamente:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://seu-dominio`
- credenciais reais de banco
- credenciais reais de SMTP
- `APP_KEY`

3. Em producao, mantenha:

- `DB_HOST=db`
- `REDIS_HOST=redis`

porque os servicos se comunicam pela rede interna do Compose.

## Como rodar o sistema

### Subida rapida em desenvolvimento

```bash
cp .env.dev.example .env.dev
make build
make up
```

Depois, em outro terminal:

```bash
make art key:generate
```

URLs uteis:

- app: `http://localhost:8080`
- Vite: `http://localhost:5173`

### Fluxo recomendado no primeiro uso

1. copiar `.env.dev.example` para `.env.dev`
2. revisar variaveis de banco, Redis, mail e OSM
3. rodar `make build`
4. rodar `make up`
5. gerar `APP_KEY` com `make art key:generate`
6. se necessario, reconstruir o banco com:

```bash
make reset-db
```

### O que e automatico em `dev`

Ao subir o `app`, o `entrypoint` ja faz:

- sincronizacao do Composer
- bootstrap do Laravel
- `storage:link`
- `migrate --force`

O que nao e automatico:

- `key:generate`
- `db:seed`
- `migrate:fresh --seed`

### Seeders

O projeto nao roda seed automaticamente no `up`.

Comandos uteis:

```bash
make seed
make reset-db
```

Observacao:

- `make reset-db` executa `migrate:fresh --seed`
- `make seed` apenas roda `db:seed` sobre a base atual

## Comandos uteis do Makefile

Arquivo: [Makefile](/home/marley/Projetos/GNAI/Makefile:1)

### Containers

```bash
make build
make up
make down
make down-v
make logs
make logs-app
```

### Laravel

```bash
make art migrate:status
make art optimize:clear
make art key:generate
make migrate
make seed
make reset-db
make tinker
```

### Frontend

```bash
make npm-dev
make npm-build
```

### Banco

```bash
make db
```

### Backup

```bash
make backup
make backup-db
make list-bkp
make restore-db FILE=arquivo.sql
make restore-full FILE=arquivo.zip
```

## Scheduler, fila e backup

### Scheduler

O container `scheduler` roda continuamente:

```bash
php artisan schedule:work
```

Isso e o que permite executar tarefas agendadas sem depender de cron no host.

### Backup automatico

O agendamento esta em [routes/console.php](/home/marley/Projetos/GNAI/routes/console.php:1):

- `backup:clean` diariamente as `12:00`
- `backup:run` diariamente as `12:05`
- timezone da tarefa: `America/Bahia`

Depois do backup, o sistema ainda chama o `BackupService::sync()` no `onSuccess`.

### Fila

Em producao existe um container `queue` dedicado, iniciado com:

```bash
sh /var/www/docker/php/entrypoint.sh queue
```

Ele executa:

```bash
php artisan queue:work --sleep=3 --tries=3 --max-time=3600
```

## Arquivos importantes do Docker

- [Dockerfile](/home/marley/Projetos/GNAI/Dockerfile:1)
- [docker-compose.dev.yml](/home/marley/Projetos/GNAI/docker-compose.dev.yml:1)
- [docker-compose.prod.yml](/home/marley/Projetos/GNAI/docker-compose.prod.yml:1)
- [docker/php/entrypoint.sh](/home/marley/Projetos/GNAI/docker/php/entrypoint.sh:1)
- [docker/php/www.conf](/home/marley/Projetos/GNAI/docker/php/www.conf:1)
- [docker/mysql/my.dev.cnf](/home/marley/Projetos/GNAI/docker/mysql/my.dev.cnf:1)
- [docker/mysql/my.prod.cnf](/home/marley/Projetos/GNAI/docker/mysql/my.prod.cnf:1)
- [docker/mysql/init/01-auth-plugin.sh](/home/marley/Projetos/GNAI/docker/mysql/init/01-auth-plugin.sh:1)
- [docker/redis/redis.conf](/home/marley/Projetos/GNAI/docker/redis/redis.conf:1)
- [nginx/nginx.conf](/home/marley/Projetos/GNAI/nginx/nginx.conf:1)

## Observacoes importantes

- o build da imagem PHP nao depende do banco
- `app` e `scheduler` compartilham a mesma imagem PHP
- em `dev`, o Vite roda em container separado
- o MySQL usa script de inicializacao para ajustar autenticacao do usuario da aplicacao, o que tambem evita falhas no dump de backup
- alguns warnings de `Redis` e `MySQL` podem depender do host ou da imagem oficial e nao necessariamente indicam problema real do projeto

## Problemas comuns

### O app subiu mas falta `APP_KEY`

Rode:

```bash
make art key:generate
```

### Quero recriar o banco do zero

Rode:

```bash
make down-v
make up
make reset-db
```

### O CSS/JS nao refletiu uma mudanca

Se estiver em `dev`, confirme se o container `node` esta ativo e se o Vite esta acessivel em `5173`.

### O backup falhou por autenticacao MySQL

O projeto ja possui o script [01-auth-plugin.sh](/home/marley/Projetos/GNAI/docker/mysql/init/01-auth-plugin.sh:1) para ajustar o usuario da aplicacao em bancos novos. Se o volume do MySQL ja existia antes da mudanca, pode ser necessario recriar o banco ou ajustar o usuario manualmente.

## Documentacao complementar

Documentos do modulo `Radar Inclusivo` em:

- [Docs/inclusive-radar/accessibility-features.md](/home/marley/Projetos/GNAI/Docs/inclusive-radar/accessibility-features.md)
- [Docs/inclusive-radar/accessible-educational-materials.md](/home/marley/Projetos/GNAI/Docs/inclusive-radar/accessible-educational-materials.md)
- [Docs/inclusive-radar/loans.md](/home/marley/Projetos/GNAI/Docs/inclusive-radar/loans.md)
- [Docs/inclusive-radar/waitlists.md](/home/marley/Projetos/GNAI/Docs/inclusive-radar/waitlists.md)
- [Docs/inclusive-radar/inspections.md](/home/marley/Projetos/GNAI/Docs/inclusive-radar/inspections.md)
- [Docs/inclusive-radar/barriers.md](/home/marley/Projetos/GNAI/Docs/inclusive-radar/barriers.md)
- [Docs/inclusive-radar/institutions.md](/home/marley/Projetos/GNAI/Docs/inclusive-radar/institutions.md)
- [Docs/inclusive-radar/locations.md](/home/marley/Projetos/GNAI/Docs/inclusive-radar/locations.md)
