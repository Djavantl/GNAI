# -----------------------------
# Ambiente (padrão: dev)
# uso: make up ENV=prod
# -----------------------------
ENV ?= dev

ifeq ($(ENV),prod)
  COMPOSE  = docker compose --env-file .env.prod -f docker-compose.prod.yml
  ENV_FILE = .env.prod
else
  COMPOSE  = docker compose --env-file .env.dev -f docker-compose.dev.yml
  ENV_FILE = .env.dev
endif

PROD_COMPOSE = docker compose --env-file .env.prod -f docker-compose.prod.yml
PROD_IMAGE   = gnai-php:prod

# -----------------------------
# Declarando regras PHONY
# -----------------------------
.PHONY: up down down-v build logs art migrate seed perm make tinker scheduler \
        coverage db backup backup-db list-bkp restore-db restore-full \
        build-assets dev-assets deploy composer storage-link \
        cache-dev cache-prod npm-build npm-dev logs-app sync-public-build host-storage-link \
        permissions-sync permissions-prune

# -----------------------------
# Contêineres
# -----------------------------
up:
	$(COMPOSE) up -d

down:
	$(COMPOSE) down

down-v:
	$(COMPOSE) down -v

build:
	$(COMPOSE) build --no-cache

logs:
	$(COMPOSE) logs -f

logs-app:
	$(COMPOSE) logs app -f --tail=50

# -----------------------------
# Artisan (comandos livres)
# ex: make art tinker
#     make art queue:work
# -----------------------------
art:
	$(COMPOSE) exec app php artisan $(filter-out $@,$(MAKECMDGOALS))

tinker:
	$(COMPOSE) exec app php artisan tinker

scheduler:
	$(COMPOSE) exec app php artisan schedule:work

perm:
	sudo chown -R $(USER):$(USER) .

make:
	$(COMPOSE) exec app php artisan make:migration $(filter-out $@,$(MAKECMDGOALS))

# -----------------------------
# PHP/Laravel - Comandos Diretos
# -----------------------------
composer:
	$(COMPOSE) exec app composer install

storage-link:
	$(COMPOSE) exec app php artisan storage:link

cache-dev:
	$(COMPOSE) exec app php artisan config:clear
	$(COMPOSE) exec app php artisan cache:clear
	$(COMPOSE) exec app php artisan view:clear

cache-prod:
	$(COMPOSE) exec app php artisan config:cache
	$(COMPOSE) exec app php artisan route:cache
	$(COMPOSE) exec app php artisan view:cache

migrate:
	$(COMPOSE) exec app php artisan migrate

seed:
	$(COMPOSE) exec app php artisan db:seed

reset-db:
	$(COMPOSE) exec app php artisan migrate:fresh --seed

permissions-sync:
	$(COMPOSE) exec app php artisan permissions:sync

permissions-prune:
	$(COMPOSE) exec app php artisan permissions:sync --prune

npm-build:
ifeq ($(ENV),prod)
	$(PROD_COMPOSE) build app
	$(MAKE) sync-public-build
else
	$(COMPOSE) exec node npm run build
endif

npm-dev:
	$(COMPOSE) exec node npm run dev

# -----------------------------
# Frontend
# -----------------------------
dev-assets:
	$(COMPOSE) up -d node

build-assets:
	npm run build

# -----------------------------
# PHPUnit / Testes
# -----------------------------
coverage:
	$(COMPOSE) exec app sh -lc 'mkdir -p /var/www/coverage && XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-html /var/www/coverage'

# -----------------------------
# Banco de dados
# -----------------------------

# Carrega o .env correto para as variáveis de banco
ifneq (,$(wildcard $(ENV_FILE)))
    include $(ENV_FILE)
    export
endif

BKP_DIR = $(or $(BACKUP_PATH),storage/app/private/$(or $(BACKUP_DISK_NAME),GNAIbackups))

db:
	$(COMPOSE) exec db mysql -u$(DB_USERNAME) -p$(DB_PASSWORD) $(DB_DATABASE)

# -----------------------------
# Backup & Restore
# -----------------------------
backup:
	$(COMPOSE) exec app php artisan backup:run

backup-db:
	@mkdir -p $(BKP_DIR)/db
	-@$(COMPOSE) exec db sh -c 'exec mysqldump -u$(DB_USERNAME) -p$(DB_PASSWORD) $(DB_DATABASE) 2>/dev/null' > $(BKP_DIR)/db/database-$(shell date +%Y-%m-%d_%H-%M-%S).sql
	@echo "✅ Backup do banco salvo em $(BKP_DIR)/db"

list-bkp:
	$(COMPOSE) exec app php artisan backup:list

restore-db:
	@if [ -z "$(FILE)" ]; then \
		echo "❌ Erro: Informe o arquivo. Ex: make restore-db FILE=meu-backup.sql"; \
		exit 1; \
	fi
	@if [ -f "$(FILE)" ]; then \
		BKP_FILE="$(FILE)"; \
	elif [ -f "$(BKP_DIR)/db/$(FILE)" ]; then \
		BKP_FILE="$(BKP_DIR)/db/$(FILE)"; \
	else \
		echo "❌ Arquivo '$(FILE)' não encontrado."; \
		exit 1; \
	fi; \
	echo "📦 Restaurando de: $$BKP_FILE..."; \
	$(COMPOSE) exec -T db sh -c 'exec mysql -u$(DB_USERNAME) -p$(DB_PASSWORD) $(DB_DATABASE)' < $$BKP_FILE
	@echo "✅ Banco restaurado com sucesso!"

restore-full:
	@if [ -z "$(FILE)" ]; then \
		echo "❌ Erro: Informe o zip. Ex: make restore-full FILE=nome.zip"; \
		exit 1; \
	fi; \
	BKP_FILE="$(BKP_DIR)/$(FILE)"; \
	if [ ! -f "$$BKP_FILE" ]; then \
		echo "❌ Arquivo '$$BKP_FILE' não encontrado."; \
		exit 1; \
	fi; \
	echo "📦 Iniciando restore total de $$BKP_FILE..."; \
	rm -rf restore-temp && mkdir -p restore-temp; \
	unzip -q $$BKP_FILE -d restore-temp; \
	echo "  -> Restaurando Banco de Dados..."; \
	SQL_FILE=$$(find restore-temp/db-dumps -name "*.sql" | head -n 1); \
	if [ -n "$$SQL_FILE" ]; then \
		$(COMPOSE) exec -T db sh -c "exec mysql -u$(DB_USERNAME) -p$(DB_PASSWORD) $(DB_DATABASE) 2>/dev/null" < $$SQL_FILE; \
	else \
		echo "     ⚠️ SQL não encontrado."; \
	fi; \
	echo "  -> Restaurando Storage..."; \
	if [ -d "restore-temp/var/www/storage/app" ]; then \
		cp -R restore-temp/var/www/storage/app/* storage/app/; \
	else \
		echo "     ⚠️ Storage não encontrado no ZIP."; \
	fi; \
	rm -rf restore-temp; \
	echo "✅ Sistema restaurado com sucesso!"

# -----------------------------
# Deploy prod
# -----------------------------
sync-public-build:
	@tmp_container=$$(docker create $(PROD_IMAGE)); \
	rm -rf public/build; \
	docker cp "$$tmp_container:/var/www/public/build" public/build; \
	docker rm "$$tmp_container" >/dev/null; \
	chmod -R u+rwX,go+rX public/build; \
	echo "✅ public/build sincronizado a partir da imagem $(PROD_IMAGE)"

host-storage-link:
	@if [ -L public/storage ] || [ ! -e public/storage ]; then \
		rm -f public/storage; \
		ln -s ../storage/app/public public/storage; \
		echo "✅ public/storage aponta para ../storage/app/public"; \
	else \
		echo "❌ public/storage existe e não é symlink. Remova/backup manualmente antes de continuar."; \
		exit 1; \
	fi

deploy:
	@echo "🚀 Iniciando deploy em produção..."
	$(PROD_COMPOSE) build --no-cache
	$(MAKE) sync-public-build
	$(MAKE) host-storage-link
	$(PROD_COMPOSE) up -d
	$(MAKE) migrate ENV=prod
	$(MAKE) permissions-sync ENV=prod
	@echo "✅ Deploy finalizado!"

# -----------------------------
# Evita conflito com arquivos
# -----------------------------
%:
	@:
