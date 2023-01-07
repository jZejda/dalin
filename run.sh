#!/bin/bash

set -e

BOLD=$(tput -T ansi bold)
NORMAL=$(tput -T ansi sgr0)

help () {
    echo "Helper script to cimplyfy command

Usage: run.sh COMMAND

Commands:
    phpstan                 Run PHPStan - LaraStan
    phpstan-baseline        Update PHPStan baseline
    phpstan-clear           Clear PHPStan baseline
    lint                    Run PHPCodeSniffer linter
    lint-fix                Run PHPCodeSniffer autofixer
    idehelper               Run IDE helper generate file"

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
        echo "${BOLD}Run Linter${NORMAL}"
         ./vendor/bin/phpcs
        ;;
    lint-auto)
        echo "${BOLD}Run Linter autofixer${NORMAL}"
        ./vendor/bin/phpcbf
        ;;
    idehelper)
        echo "${BOLD}Run IDE helper generate file...${NORMAL}"
        php artisan ide-helper:generate
    ;;
    help)
        help
        ;;
    *)
        help
        ;;
esac
