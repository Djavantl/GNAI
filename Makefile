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

perm:
	sudo chown -R $USER:$USER .

make:
	docker compose exec app php artisan make:migration $(filter-out $@,$(MAKECMDGOALS))

# Isso impede que o 'make' confunda os comandos com arquivos
%:
	@:
