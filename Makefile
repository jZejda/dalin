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
PEST = $(SAIL) $(ARTISAN) test
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
	@$(SAIL) up --detach --remove-orphans

down: ## Stop the docker hub
	@$(SAIL) down --remove-orphans

bash: ## Connect to PHP container via bash so up and down arrows go to previous commands
	@$(SAIL) bash

clear: ## Clear various caches
	@$(SAIL) $(COMPOSER) dump-autoload
	@$(SAIL) $(ARTISAN) route:clear
	@$(SAIL) $(ARTISAN) view:clear
	@$(SAIL) $(ARTISAN) config:clear
	@$(SAIL) $(ARTISAN) clear-compiled
	@$(SAIL) $(ARTISAN) permission:cache-reset
	@$(SAIL) $(ARTISAN) optimize

migrate-test-database: ## Migrate test database
#	@$(SAIL) $(ARTISAN) migrate:rollback --env=testing
	@$(SAIL) $(ARTISAN) migrate --env=testing --seed

lint: ## Run the PHP linter
	@$(PINT)

lint-dirty: ## Run the PHP linter only on uncommited changes
	@$(PINT) --dirty

lint-fix: ## Run the PHP linter and repair the errors
	@$(PINT) --repair

phpstan: ## Run the PHPStan static analyzer
	@$(PHPSTAN)	analyse --memory-limit=2G

phpstan-baseline: ## Regenerate the PHPStan baseline (commit it with the fix)
	@$(PHPSTAN)	analyse --generate-baseline --memory-limit=2G

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
pest: ## Run the PHP Pest test (migrates test database first)
	@$(SAIL) $(ARTISAN) migrate --env=testing --force
	@$(PEST)

pest-drift: ## Run the PHP Pest test convert from UnitTest
	@$(PINT) --drift

## —— NPM 💖 ———————————————————————————————————————————————————————————————————————————————————————————————————————————
npm-dev: ## Run the PHP Pest test convert from UnitTest
	@$(SAIL) $(NPM) run dev

## —— Demo 🎭 ——————————————————————————————————————————————————————————————————————————————————————————————————————————
demo-reset: ## Full demo reset: migrate:fresh + DemoSeeder (requires DEMO_MODE=true in .env)
	@$(SAIL) $(ARTISAN) demo:reset

demo-seed: ## Run DemoSeeder only, without migrate:fresh (requires clean DB + DEMO_MODE=true in .env)
	@$(SAIL) $(ARTISAN) db:seed --class=DemoSeeder --force

## —— Deploy 🚀 —————————————————————————————————————————————————————————————————————————————————————————————————————————
DEP = ./vendor/bin/dep

deploy: ## Deploy site, example: make deploy s=demo (viz docs/deployment.md)
	@$(eval s ?=)
	@$(DEP) deploy $(s)

deploy-tag: ## Deploy a tag, example: make deploy-tag s=demo t=v13.0.1
	@$(eval s ?=)
	@$(eval t ?=)
	@$(DEP) deploy $(s) --tag=$(t)

deploy-rollback: ## Rollback to previous release, example: make deploy-rollback s=demo
	@$(eval s ?=)
	@$(DEP) rollback $(s)

deploy-status: ## Show deployed revision, example: make deploy-status s=demo
	@$(eval s ?=)
	@$(DEP) app:version $(s)

deploy-releases: ## List releases kept on a site, example: make deploy-releases s=demo
	@$(eval s ?=)
	@$(DEP) releases $(s)

deploy-logs: ## Tail application log on a site, example: make deploy-logs s=demo
	@$(eval s ?=)
	@$(DEP) logs:app $(s)
