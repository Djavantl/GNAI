# Makefile para o projeto GNAI

up:
	docker compose up -d

down:
	docker compose down

art:
	docker compose exec app php artisan $(filter-out $@,$(MAKECMDGOALS))

migrate:
	docker compose exec app php artisan migrate

seed:
	docker-compose exec app php artisan migrate:fresh --seed

# Isso impede que o 'make' confunda os comandos com arquivos
%:
	@:
