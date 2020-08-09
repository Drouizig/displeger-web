DOCKER_COMPOSE = docker-compose

docker_env_dev:
ifeq ($(ENV),dev)
        DOCKER_COMPOSE = docker-compose -f docker-compose.yml
endif

DOCKER_COMPOSE_EXEC	   = $(DOCKER_COMPOSE) exec -T
DOCKER_COMPOSE_UP	   = $(DOCKER_COMPOSE) up -d --remove-orphans --no-recreate

EXEC_PHP	   = $(DOCKER_COMPOSE_EXEC) php gosu foo
EXEC_DATABASE  = $(DOCKER_COMPOSE_EXEC) database

SYMFONY		   = $(EXEC_PHP) php bin/console
COMPOSER	   = $(EXEC_PHP) composer

##
## File dependencies
## -----------------
##

composer.lock: ## Update Composer dependencies
	$(COMPOSER) update

vendor: composer.lock ## Install Composer dependencies
	$(COMPOSER) install --no-progress

composer-install-no-scripts: composer.lock ## Install Composer dependencies without running scripts
	$(COMPOSER) install --no-progress --no-scripts

##
## Project
## -------
##

shell: ## Enter in web container
	$(DOCKER_COMPOSE) exec php gosu foo sh

install: database assets ## Install everything

clean: start ## Remove dependencies and built resources
	$(DOCKER_COMPOSE) exec php rm -Rf public/build/*
	$(DOCKER_COMPOSE) exec php rm -Rf vendor
	$(DOCKER_COMPOSE) exec php rm -Rf var/cache/ var/log var/app/sessions
	$(DOCKER_COMPOSE) stop
	$(DOCKER_COMPOSE) kill
	$(DOCKER_COMPOSE) down --volumes --remove-orphans		

reset: clean build start install ## Re-install all dependencies, clean cache, etc.

stop: ## Stop the project
	$(DOCKER_COMPOSE) stop

start: ## Start Docker containers
	$(DOCKER_COMPOSE_UP)

start-php: ## Start Docker container of PHP
	$(DOCKER_COMPOSE_UP) php

start-mysql: ## Start Docker container of PHP
	$(DOCKER_COMPOSE_UP) mysql

restart: ## Restart the project
	$(DOCKER_COMPOSE) restart

build:
	$(DOCKER_COMPOSE) pull --quiet --ignore-pull-failures
	$(DOCKER_COMPOSE) build --pull

##
## Utils
## -----
##

assets: vendor ## Build assets
	yarn run dev

cache-warmup: vendor ## Warmup caches
	$(SYMFONY) cache:warmup --env=dev

cache-clear: vendor ## Clear caches
	$(SYMFONY) cache:clear --env=dev

fix-permissions: ## Fix file permissions
	$(DOCKER_COMPOSE) exec -T php mkdir -p var/cache var/cache/dev var/cache/test var/log var/sessions web/assets web/bundles web/css web/js web/upload
	$(DOCKER_COMPOSE) exec -T php touch var/log/dev.log var/log/test.log var/log/pi_history.log var/log/salesforce.log var/log/import.log
	$(DOCKER_COMPOSE) exec -T php chown -R foo:bar var/cache var/cache/dev var/cache/test var/log var/sessions web/assets web/bundles web/css web/js web/upload
	$(DOCKER_COMPOSE) exec -T php chmod -R 777 var/cache var/log var/sessions web/assets web/bundles web/css web/js web/upload

database: vendor ## Build the database
	$(SYMFONY) doctrine:database:drop --if-exists --force
	$(SYMFONY) doctrine:database:create --if-not-exists
	$(EXEC_DATABASE) sh -c "gunzip -k -c /database/displeger_dump.sql.gz > /database/displeger_dump.sql" 
	$(EXEC_DATABASE) sh -c "psql -U postgres displeger < /database/displeger_dump.sql"
	rm -f docker/database/displeger_dump.sql


translations: vendor ## Update the translation files
	$(SYMFONY) tran:up br --output-format=xlf --force
	$(SYMFONY) tran:up fr --output-format=xlf --force
	$(SYMFONY) tran:up en --output-format=xlf --force

##
## Quality
## -----
##

php-stan: ## Perform PHPStan check
	$(EXEC_PHP) vendor/phpstan/phpstan/phpstan analyse --no-progress --level 0 -c phpstan.neon src

php-cs-fixer: ## Run PHP-CS fixer
	docker run -t --volume $(PWD):/project --workdir /project jakzal/phpqa:php7.1-alpine ci/php-cs-fixer
##
## Tests
## -----
##

phpunit: ## Run Behat tests without resetting database
	$(EXEC_PHP) bin/phpunit