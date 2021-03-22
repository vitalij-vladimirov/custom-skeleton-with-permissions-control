start:
	docker-compose up -d

stop:
	docker-compose down

destroy:
	docker-compose down -v

rebuild: destroy start setup

setup:
	docker-compose exec app bash -c "composer install"

first-run: start setup
