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
# Backup & Restore
# -----------------------------

# Backup apenas do DB
backup-db:
	mkdir -p storage/app/private/GNAIbackups
	docker compose exec db sh -c 'exec mysqldump -uroot -p$$MYSQL_ROOT_PASSWORD $$MYSQL_DATABASE' > storage/app/private/GNAIbackups/database-$(shell date +%Y-%m-%d_%H-%M-%S).sql

# Restore do DB
restore-db:
	@if [ -z "$(FILE)" ]; then \
		echo "Use: make restore-db FILE=arquivo.sql"; \
		exit 1; \
	fi
	docker compose exec -T db sh -c 'exec mysql -uroot -p$$MYSQL_ROOT_PASSWORD $$MYSQL_DATABASE' < $(FILE)

# Backup completo (DB + storage) em ZIP
backup-full:
	@echo "Criando backup completo..."
	@mkdir -p storage/app/private/GNAIbackups
	@docker compose exec db sh -c 'exec mysqldump -uroot -p$$MYSQL_ROOT_PASSWORD $$MYSQL_DATABASE' > storage/app/private/GNAIbackups/database.sql
	@tar -czf storage/app/private/GNAIbackups/storage.tar.gz -C storage/app private
	@zip -r storage/app/private/GNAIbackups/backup-$(shell date +%Y-%m-%d_%H-%M-%S).zip storage/app/private/GNAIbackups/database.sql storage/app/private/GNAIbackups/storage.tar.gz
	@rm storage/app/private/GNAIbackups/database.sql storage/app/private/GNAIbackups/storage.tar.gz
	@echo "Backup completo gerado em storage/app/private/GNAIbackups"

# Restore completo a partir do ZIP
restore-full:
	@if [ -z "$(FILE)" ]; then \
		echo "Use: make restore-full FILE=backup.zip"; \
		exit 1; \
	fi
	@echo "Iniciando restore do backup $(FILE)..."
	@mkdir -p restore-temp
	@unzip -q $(FILE) -d restore-temp
	@echo "Restaurando banco de dados..."
	@docker compose exec -T db sh -c 'exec mysql -uroot -p$$MYSQL_ROOT_PASSWORD $$MYSQL_DATABASE' < restore-temp/database.sql
	@echo "Restaurando storage..."
	@tar -xzf restore-temp/storage.tar.gz -C storage/app
	@rm -rf restore-temp
	@echo "Restore completo!"

# -----------------------------
# Evita conflito com arquivos
# -----------------------------
%:
	@:
