#!/bin/bash

set -e

BOLD=$(tput -T ansi bold)
NORMAL=$(tput -T ansi sgr0)

help () {
    echo "Helper script to simplify command

Usage: run.sh COMMAND

Commands:
    up                      Run Sail UP as demon
    down                    Stop Sail
    ideahelper              Generate IdeaHelper files
    phpstan                 Run PHPStan - LaraStan
    phpstan-baseline        Update PHPStan baseline
    phpstan-clear           Clear PHPStan baseline
    lint                    Run PINT linter - autoFixer
    idehelper               Run IDE helper generate file
    deploy                  Prepare to deploy
    postdeploy              Run post deploy procedure"

}

case "$1" in

    up)
        echo "${BOLD}Run Docker Laravel Sail ...${NORMAL}"
        vendor/bin/sail up -d
        ;;
    down)
        echo "${BOLD}Stop Docker Laravel Sail ...${NORMAL}"
        vendor/bin/sail down
        ;;
    idehelper)
        echo "${BOLD}Regenerate IdeaHelper ...${NORMAL}"
        mkdir -p storage/idea
        vendor/bin/sail artisan ide-helper:generate
        vendor/bin/sail artisan ide-helper:models
        vendor/bin/sail artisan ide-helper:meta
        ;;
    phpstan)
        echo "${BOLD}Run PHPStan ...${NORMAL}"
        vendor/bin/phpstan analyse --memory-limit=2G
        ;;
    phpstan-baseline)
        echo "${BOLD}Run PHPStan baseline...${NORMAL}"
        vendor/bin/phpstan analyse --generate-baseline --memory-limit=4G
        ;;
    phpstan-clear)
        echo "${BOLD}Run PHPStan baseline...${NORMAL}"
        vendor/bin/phpstan analyse . tests clear-result-cache --ansi --memory-limit=2G --generate-baseline "${@:2}"
        ;;
    lint)
        echo "${BOLD}Run Linter PINT${NORMAL}"
        ./vendor/bin/pint
        ;;
    deploy)
        echo "${BOLD}Run Deploy to product server...${NORMAL}"
        vendor/bin/sail artisan config:cache
        vendor/bin/sail artisan event:cache
        vendor/bin/sail artisan route:cache
        vendor/bin/sail artisan view:cache
        vendor/bin/sail npm run build
    ;;
    postdeploy)
        echo "${BOLD}Run PostDeploy on product server...${NORMAL}"
        php8.1 artisan config:cache
        php8.1 artisan event:cache
        php8.1 artisan route:cache
        php8.1 artisan view:cache
        npm run build
    ;;
    help)
        help
        ;;
    *)
        help
        ;;
esac
