#!/bin/bash

set -e

BOLD=$(tput -T ansi bold)
NORMAL=$(tput -T ansi sgr0)

help () {
    echo "Helper script to simplify command

Usage: run.sh COMMAND

Commands:
    phpstan                 Run PHPStan - LaraStan
    phpstan-baseline        Update PHPStan baseline
    phpstan-clear           Clear PHPStan baseline
    lint                    Run PINT linter - autoFixer
    idehelper               Run IDE helper generate file
    deploy                  Prepare to deploy
    postdeploy              Run post deploy procedure"

}

case "$1" in

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
    idehelper)
        echo "${BOLD}Run IDE helper generate file...${NORMAL}"
        php artisan ide-helper:generate
        php artisan ide-helper:model
    ;;
    deploy)
        echo "${BOLD}Run Deploy to product server...${NORMAL}"
        php artisan config:cache
        php artisan event:cache
        php artisan route:cache
        php artisan view:cache
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
