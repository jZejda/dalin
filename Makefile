# Executables (local)
DOCKER_COMP = docker compose

# Laravel Sail
SAIL     = ./vendor/bin/sail

# Docker containers
PHP_CONT = $(DOCKER_COMP) exec php

# Executables
COMPOSER = composer
ARTISAN  = artisan
PINT = ./vendor/bin/pint
PHPSTAN = ./vendor/bin/phpstan
PEST = ./vendor/bin/pest
NPM = npm

# Misc
.DEFAULT_GOAL = help
.PHONY        : help build up start down logs sh composer vendor sf cc test

## —— 🎵 🐳 The Symfony Docker Makefile 🐳 🎵 ——————————————————————————————————————————————————————————————————————————
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9\./_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Docker 🐳 ————————————————————————————————————————————————————————————————————————————————————————————————————————
build: ## Builds the Docker images
	@$(DOCKER_COMP) build --pull --no-cache

up: ## Start the docker hub in detached mode (no logs)
	@$(SAIL) up --detach

down: ## Stop the docker hub
	@$(SAIL) down --remove-orphans

bash: ## Connect to PHP container via bash so up and down arrows go to previous commands
	@$(SAIL) bash

lint: ## Run the PHP linter
	@$(PINT)

lint-dirty: ## Run the PHP linter only on uncommited changes
	@$(PINT) --dirty

lint-fix: ## Run the PHP linter and repair the errors
	@$(PINT) --repair

phpstan: ## Run the PHPStan static analyzer
	@$(PHPSTAN)	analyse --memory-limit=2G

phpstan-baseline: ## Run the PHPStan static analyzer
	@$(PHPSTAN)	analyz --generate-baseline

## —— Composer 🧙 ——————————————————————————————————————————————————————————————————————————————————————————————————————
composer: ## Run composer, pass the parameter "c=" to run a given command, example: make composer c='req symfony/orm-pack'
	@$(eval c ?=)
	@$(SAIL) $(COMPOSER) $(c)

vendor: ## Install vendors according to the current composer.lock file
vendor: c=install --prefer-dist --no-dev --no-progress --no-scripts --no-interaction
vendor: composer

## —— Laravel artisan 🧊 ———————————————————————————————————————————————————————————————————————————————————————————————
art: ## List all Laravel commands or pass the parameter "c=" to run a given command, example: make at c=about
	@$(eval c ?=)
	@$(SAIL) $(ARTISAN) $(c)

## —— PHP-Pest test 🥰 —————————————————————————————————————————————————————————————————————————————————————————————————
pest: ## ## Run the PHP Pest test
	@$(PEST)

pest-drift: ## Run the PHP Pest test convert from UnitTest
	@$(PINT) --drift

## —— NPM 💖 ———————————————————————————————————————————————————————————————————————————————————————————————————————————
npm-dev: ## Run the PHP Pest test convert from UnitTest
	@$(SAIL) $(NPM) run dev
