# -----------------------------
# Ambiente (padrão: dev)
# uso: make up ENV=prod
# -----------------------------
ENV ?= dev

ifeq ($(ENV),prod)
  COMPOSE  = docker compose -f docker-compose.prod.yml
  ENV_FILE = .env.prod
else
  COMPOSE  = docker compose -f docker-compose.dev.yml
  ENV_FILE = .env.dev
endif

APP_CONTAINER = gnai_app

# -----------------------------
# Declarando regras PHONY
# -----------------------------
.PHONY: up down down-v build logs art migrate seed perm make tinker scheduler \
        coverage db backup backup-db list-bkp restore-db restore-full \
        build-assets dev-assets deploy composer storage-link \
        cache-dev cache-prod npm-build logs-app

# -----------------------------
# Contêineres
# -----------------------------
up:
	$(COMPOSE) up

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
# commands.sh — PHP/Laravel
# -----------------------------
composer:
	$(COMPOSE) exec app sh /usr/local/bin/commands.sh composer

storage-link:
	$(COMPOSE) exec app sh /usr/local/bin/commands.sh storage-link

cache-dev:
	$(COMPOSE) exec app sh /usr/local/bin/commands.sh cache-dev

cache-prod:
	$(COMPOSE) exec app sh /usr/local/bin/commands.sh cache-prod

migrate:
	$(COMPOSE) exec app sh /usr/local/bin/commands.sh migrate

seed:
	$(COMPOSE) exec app sh /usr/local/bin/commands.sh seed

npm-build:
	$(COMPOSE) exec app sh /usr/local/bin/commands.sh npm-build

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
	docker exec -it $(APP_CONTAINER) ./vendor/bin/phpunit --coverage-html /var/www/coverage

# -----------------------------
# Banco de dados
# -----------------------------

# Carrega o .env correto para as variáveis de banco
ifneq (,$(wildcard $(ENV_FILE)))
    include $(ENV_FILE)
    export
endif

BKP_DIR = $(BACKUP_PATH)

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
deploy:
	@echo "🚀 Iniciando deploy em produção..."
	$(MAKE) npm-build ENV=prod
	$(COMPOSE) build --no-cache
	$(COMPOSE) up -d
	$(MAKE) migrate ENV=prod
	@echo "✅ Deploy finalizado!"

# -----------------------------
# Evita conflito com arquivos
# -----------------------------
%:
	@:
