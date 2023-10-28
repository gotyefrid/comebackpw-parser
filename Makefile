build:
	docker-compose -f ./docker-compose.yml up -d --build

rebuild:
	docker-compose -f ./docker-compose.yml down
	docker-compose -f ./docker-compose.yml up -d --build

down:
	docker-compose -f ./docker-compose.yml down

start:
	docker-compose -f ./docker-compose.yml up -d

recreate:				## Пересобрать все контейнеры игнорируя кеш
	docker-compose -f ./docker-compose.yml down
	docker-compose -f ./docker-compose-dev.yml up -d --force-recreate

run-php:				## Зайти в консоль контейнера PHP
	docker exec -it comebackpw-parser-php sh
