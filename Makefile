# Makefile para o projeto GNAI

# -----------------------------
# Declarando regras “PHONY” para sempre executar
# -----------------------------
.PHONY: up down art migrate seed perm make tinker scheduler coverage \
        db backup-db restore-db backup-full restore-full

# -----------------------------
# Contêineres principais
# -----------------------------
up:
	docker compose up -d

down:
	docker compose down

# -----------------------------
# Artisan
# -----------------------------
art:
	docker compose exec app php artisan $(filter-out $@,$(MAKECMDGOALS))

migrate:
	docker compose exec app php artisan migrate

seed:
	docker compose exec app php artisan migrate:fresh --seed

perm:
	sudo chown -R $USER:$USER .

make:
	docker compose exec app php artisan make:migration $(filter-out $@,$(MAKECMDGOALS))

tinker:
	docker compose exec app php artisan tinker

scheduler:
	docker compose exec app php artisan schedule:work

# -----------------------------
# PHPUnit / Testes
# -----------------------------
coverage:
	docker exec -it gnai_app ./vendor/bin/phpunit --coverage-html /var/www/coverage

# -----------------------------
# Banco de dados MySQL
# -----------------------------
db:
	docker compose exec db mysql -u gnai_user -pgnai2026ads gnai_db

# -----------------------------
# Configurações Iniciais
# -----------------------------

# Tenta carregar o arquivo .env se ele existir
ifneq (,$(wildcard ./.env))
    include .env
    export
endif

# Define o diretório de backup usando a variável do .env (com fallback)
BKP_DIR = $(BACKUP_PATH)

# -----------------------------
# Backup & Restore
# -----------------------------

# Backup automático via Laravel (Spatie)
backup:
	docker compose exec app php artisan backup:run

# Backup apenas do DB em subpasta específica
backup-db:
	@mkdir -p $(BKP_DIR)/db
	-@docker compose exec db sh -c 'exec mysqldump -u$(DB_USERNAME) -p$(DB_PASSWORD) $(DB_DATABASE) 2>/dev/null' > $(BKP_DIR)/db/database-$(shell date +%Y-%m-%d_%H-%M-%S).sql
	@echo "✅ Backup do banco salvo em $(BKP_DIR)/db"

# Listar backups feitos pelo Laravel
list-bkp:
	docker compose exec app php artisan backup:list

# Restore apenas do Banco de Dados
restore-db:
	@if [ -z "$(FILE)" ]; then \
		echo "❌ Erro: Informe o nome do arquivo. Ex: make restore-db FILE=meu-backup.sql"; \
		exit 1; \
	fi
	@# Verifica se o arquivo existe no caminho relativo ou dentro da pasta de backups
	@if [ -f "$(FILE)" ]; then \
		BKP_FILE="$(FILE)"; \
	elif [ -f "$(BKP_DIR)/db/$(FILE)" ]; then \
		BKP_FILE="$(BKP_DIR)/db/$(FILE)"; \
	else \
		echo "❌ Erro: Arquivo '$(FILE)' não encontrado na pasta $(BKP_DIR)/db/"; \
		exit 1; \
	fi; \
	echo "📦 Restaurando de: $$BKP_FILE..."; \
	docker compose exec -T db sh -c 'exec mysql -u$(DB_USERNAME) -p$(DB_PASSWORD) $(DB_DATABASE)' < $$BKP_FILE
	@echo "✅ Banco de dados restaurado com sucesso!"

# Restore completo (Spatie Zip) - Aceita apenas o nome do arquivo
restore-full:
	@if [ -z "$(FILE)" ]; then \
		echo "❌ Erro: Informe o nome do zip. Ex: make restore-full FILE=nome.zip"; \
		exit 1; \
	fi; \
	BKP_FILE="$(BKP_DIR)/$(FILE)"; \
	if [ ! -f "$$BKP_FILE" ]; then \
		echo "❌ Erro: Arquivo '$$BKP_FILE' não encontrado."; \
		exit 1; \
	fi; \
	echo "📦 Iniciando restore total de $$BKP_FILE..."; \
	rm -rf restore-temp && mkdir -p restore-temp; \
	unzip -q $$BKP_FILE -d restore-temp; \
	\
	echo "  -> Restaurando Banco de Dados..."; \
	SQL_FILE=$$(find restore-temp/db-dumps -name "*.sql" | head -n 1); \
	if [ -n "$$SQL_FILE" ]; then \
		echo "     Encontrado: $$SQL_FILE"; \
		docker compose exec -T db sh -c "exec mysql -u$(DB_USERNAME) -p$(DB_PASSWORD) $(DB_DATABASE) 2>/dev/null" < $$SQL_FILE; \
	else \
		echo "     ⚠️ SQL não encontrado em restore-temp/db-dumps/"; \
	fi; \
	\
	echo "  -> Restaurando Arquivos (Storage)..."; \
	if [ -d "restore-temp/var/www/storage/app" ]; then \
		cp -R restore-temp/var/www/storage/app/* storage/app/; \
		echo "     Arquivos copiados para storage/app/"; \
	else \
		echo "     ⚠️ Estrutura de storage não encontrada no ZIP."; \
	fi; \
	\
	rm -rf restore-temp; \
	echo "✅ Sistema GNAI restaurado com sucesso!"

# -----------------------------
# Evita conflito com arquivos
# -----------------------------
%:
	@:
