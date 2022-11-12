docker_compose_exec = docker-compose exec -T --user "www-data"


stop:
	docker-compose down

start:
	docker-compose up -d --remove-orphans

bash:
	docker-compose exec php sh
