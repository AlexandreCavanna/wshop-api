# Executables (local)
DOCKER_COMP = docker compose

# Docker containers
PHP_CONT = $(DOCKER_COMP) exec php

# Executables
PHP      = $(PHP_CONT) php
COMPOSER = $(PHP_CONT) composer
PHPUNIT  = $(PHP) bin/phpunit
SYMFONY  = $(PHP) bin/console

# Misc
.DEFAULT_GOAL = help
.PHONY        : help build up start down logs sh composer vendor sf cc test

## —— 🎵 🐳 The Symfony Docker Makefile 🐳 🎵 ——————————————————————————————————
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9\./_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Docker 🐳 ————————————————————————————————————————————————————————————————
build: ## Builds the Docker images
	@$(DOCKER_COMP) build --pull --no-cache

up: ## Start the docker hub in detached mode (no logs)
	@$(DOCKER_COMP) up --detach

start: build up ## Build, start the containers

down: ## Stop the docker hub
	@$(DOCKER_COMP) down --remove-orphans

logs: ## Show live logs
	@$(DOCKER_COMP) logs --tail=0 --follow

sh: ## Connect to the FrankenPHP container
	@$(PHP_CONT) sh

bash: ## Connect to the FrankenPHP container via bash so up and down arrows go to previous commands
	@$(PHP_CONT) bash

init-test-db: ## Initialize the test database
	$(SYMFONY) doctrine:database:create --if-not-exists --no-interaction --env=test
	$(SYMFONY) doctrine:migrations:migrate --no-interaction --all-or-nothing --env=test

test: ## Start tests with phpunit, pass the parameter "c=" to add options to phpunit, example: make test c="--group e2e --stop-on-failure"
	@$(eval c ?=)
	$(PHPUNIT) $(c)

OS := $(shell uname -s)
trust-cert: ## Install the certificate authority ( run this only once on your machine)
ifeq ($(OS),Darwin)
	docker cp $$(docker compose ps -q php):/data/caddy/pki/authorities/local/root.crt /tmp/root.crt && \
	sudo security add-trusted-cert -d -r trustRoot -k /Library/Keychains/System.keychain /tmp/root.crt
else ifeq ($(OS),Linux)
	docker cp $$(docker compose ps -q php):/data/caddy/pki/authorities/local/root.crt /usr/local/share/ca-certificates/root.crt && \
	sudo update-ca-certificates
else
	@echo "Unsupported OS: $(OS)"
endif

## —— Composer 🧙 ——————————————————————————————————————————————————————————————
composer: ## Run composer, pass the parameter "c=" to run a given command, example: make composer c='req symfony/orm-pack'
	@$(eval c ?=)
	@$(COMPOSER) $(c)

lint: ## Run code easy coding standard fixer
	@$(COMPOSER) lint

rector: ## Run rector
	@$(COMPOSER) rector

vendor: ## Install vendors according to the current composer.lock file
vendor: c=install --prefer-dist --no-dev --no-progress --no-scripts --no-interaction
vendor: composer

## —— Symfony 🎵 ———————————————————————————————————————————————————————————————
sf: ## List all Symfony commands or pass the parameter "c=" to run a given command, example: make sf c=about
	@$(eval c ?=)
	@$(SYMFONY) $(c)

sf-logs: ## Tail Symfony logs (use make sf-logs f=prod to see prod.log)
	@$(eval f ?= dev)
	@$(PHP_CONT) sh -c 'tail -n +1 -f var/log/$(f).log'

cc: c=c:c## Clear the cache
cc: sf

fixtures: ## Load Doctrine fixtures
	@$(SYMFONY) doctrine:fixtures:load --no-interaction
