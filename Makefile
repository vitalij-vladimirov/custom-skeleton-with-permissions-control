start:
	docker-compose up -d

stop:
	docker-compose stop

destroy:
	docker-compose down -v

rebuild: destroy start

setup:
	docker-compose exec app bash -c "composer install"

first-run: start setup
