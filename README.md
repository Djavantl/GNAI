# GNAI

Sistema de Gestao do Nucleo de Acessibilidade e Inclusao do IF Baiano. A
plataforma integra dois modulos:

- Atendimento Educacional Especializado (AEE);
- Radar Inclusivo.

O projeto utiliza Laravel 12, PHP 8.2, MySQL 8, Vite e Docker Compose. Em
desenvolvimento, o acesso ocorre pelo Nginx em container. Em producao, o Apache
do servidor encaminha as requisicoes PHP ao PHP-FPM do container.

## Requisitos

- Git;
- Docker Engine;
- Docker Compose (`docker compose`);
- Make;
- Apache 2 apenas em producao.

## Desenvolvimento

### 1. Preparar o ambiente

Na raiz do projeto, crie o arquivo de configuracao:

```bash
cp .env.example .env.dev
```

Revise principalmente as variaveis abaixo:

```env
APP_ENV=local
APP_KEY=base64:chave_gerada
APP_DEBUG=true
APP_URL=http://localhost

DB_HOST=db
DB_PORT=3306
DB_DATABASE=gnai_db
DB_USERNAME=gnai_user
DB_PASSWORD=troque_esta_senha
MYSQL_ROOT_PASSWORD=troque_esta_senha
```

O arquivo `.env.dev` contem dados locais e nao deve ser versionado.
Gere o valor da chave com o comando abaixo e copie a saida completa para
`APP_KEY`:

```bash
printf 'base64:%s\n' "$(openssl rand -base64 32)"
```

### 2. Construir e iniciar

```bash
make build
make up
```

O sistema estara disponivel em:

- aplicacao: `http://localhost`;
- Vite: `http://localhost:5173`;
- phpMyAdmin opcional: `http://localhost:8081`.

Para iniciar o phpMyAdmin:

```bash
docker compose --env-file .env.dev -f docker-compose.dev.yml --profile tools up -d
```

O ambiente executa as migrations automaticamente. Para inserir os dados
iniciais:

```bash
make seed
```

Comandos mais usados:

```bash
make logs
make test
make art migrate:status
make down
```

O Nginx de desenvolvimento ocupa a porta `80`. Se o Apache estiver ativo na
mesma maquina, pare-o antes de iniciar o projeto:

```bash
sudo systemctl stop apache2
```

## Producao

Em producao, os containers executam PHP-FPM, MySQL, scheduler e fila. O Apache
fica instalado no host, entrega os arquivos de `public/` e encaminha os scripts
PHP para `127.0.0.1:9000`.

### 1. Preparar o ambiente

```bash
cp .env.prod.example .env.prod
```

Configure ao menos:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://gnai.example.edu.br

DB_HOST=db
DB_PORT=3306
DB_DATABASE=gnai_db
DB_USERNAME=gnai_user
DB_PASSWORD=troque_esta_senha
MYSQL_ROOT_PASSWORD=troque_esta_senha
```

Preencha tambem as configuracoes reais de e-mail e backup. Para gerar a
`APP_KEY`:

```bash
printf 'base64:%s\n' "$(openssl rand -base64 32)"
```

Copie o resultado completo para `APP_KEY` no `.env.prod`. Esse arquivo contem
segredos e nao deve ser versionado.

### 2. Instalar e configurar o Apache

Em Debian ou Ubuntu:

```bash
sudo apt update
sudo apt install -y apache2
sudo a2enmod proxy proxy_fcgi setenvif rewrite headers
sudo systemctl enable --now apache2
```

Crie `/etc/apache2/sites-available/gnai.conf` e ajuste os valores ficticios do
exemplo para o servidor real:

```apache
<VirtualHost *:80>
    ServerName gnai.example.edu.br
    ServerAlias gnai.interno.example.edu.br 192.0.2.10
    ServerAdmin webmaster@localhost

    DocumentRoot /srv/gnai/public
    DirectoryIndex index.php

    <Directory /srv/gnai/public>
        Options FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    <Directory /srv/gnai/storage/app/public>
        Options FollowSymLinks
        AllowOverride None
        Require all granted
    </Directory>

    <FilesMatch "\.php$">
        SetHandler "proxy:fcgi://127.0.0.1:9000"
    </FilesMatch>

    ProxyFCGISetEnvIf "true" HTTP_HOST "gnai.example.edu.br"
    ProxyFCGISetEnvIf "true" SERVER_NAME "gnai.example.edu.br"
    ProxyFCGISetEnvIf "true" SERVER_PORT "443"
    ProxyFCGISetEnvIf "true" HTTPS "on"
    ProxyFCGISetEnvIf "true" HTTP_X_FORWARDED_PROTO "https"
    ProxyFCGISetEnvIf "true" SCRIPT_FILENAME "/var/www/public%{reqenv:SCRIPT_NAME}"

    ErrorLog ${APACHE_LOG_DIR}/gnai_error.log
    CustomLog ${APACHE_LOG_DIR}/gnai_access.log combined
</VirtualHost>
```

O exemplo considera que o HTTPS e finalizado por um proxy institucional antes
do Apache. Se o Apache gerenciar o certificado, habilite `ssl` e configure um
VirtualHost `*:443`. Se o acesso for somente HTTP, remova as variaveis de HTTPS
e use uma `APP_URL` iniciada por `http://`.

Ative e valide o VirtualHost:

```bash
sudo a2ensite gnai.conf
sudo a2dissite 000-default.conf
sudo apache2ctl configtest
sudo systemctl reload apache2
```

O teste deve retornar `Syntax OK`.

### 3. Publicar o sistema

Na raiz do projeto:

```bash
make deploy
```

Esse comando constroi a imagem, publica os assets, cria o link de `storage`,
inicia os containers, executa as migrations e sincroniza as permissoes.

Confira a implantacao:

```bash
docker compose --env-file .env.prod -f docker-compose.prod.yml ps
curl -I https://gnai.example.edu.br
```

Logs uteis:

```bash
make logs-app ENV=prod
sudo tail -f /var/log/apache2/gnai_error.log
```

Depois de alterar a configuracao do Apache, valide e recarregue:

```bash
sudo apache2ctl configtest
sudo systemctl reload apache2
```

Todos os dominios, IPs e caminhos apresentados neste README sao ficticios e
devem ser substituidos pelos valores do ambiente sem publicar dados sensiveis.
