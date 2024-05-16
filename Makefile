help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

up: ## Creates the docker-compose stack.
	-docker swarm init
	docker-compose up -d

down: ## Deletes the docker-compose stack.  Your local environment will no longer be accessible.
	docker-compose down

init: ## Initialise local environment
	$(MAKE) up
	$(MAKE) composer-install
	$(MAKE) db-migrate

bash: ## Open a bash terminal for Symfony
	docker exec -it app_php bash

ca-cl: ## Clears the symfony cache
	docker exec app_php bash -c "php bin/console cache:clear"

npm: ## Install npm dependencies
	docker exec app_node sh -c "npm install"

composer-install: ## Install symfony vendors
	docker exec app_php bash -c "composer install"

db-create: ## Uses doctrine:schema:create to create database
	docker exec app_php bash -c "php bin/console doctrine:database:create"

db-migrate: ## Uses doctrine:migrations:migrate to migrate database
	docker exec app_php bash -c "php bin/console d:m:m"

.PHONY: up down ca-cl composer-install help