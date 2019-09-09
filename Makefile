.PHONY: coverage cs help infection it stan test

it: stan test ## Runs the cs, stan, and test targets

coverage: vendor ## Collects coverage from running unit tests with phpunit
	mkdir -p .build/phpunit
	vendor/bin/phpunit --dump-xdebug-filter=.build/phpunit/xdebug-filter.php
	vendor/bin/phpunit --coverage-text --prepend=.build/phpunit/xdebug-filter.php

help: ## Displays this list of targets with descriptions
	@grep -E '^[a-zA-Z0-9_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}'

infection: vendor ## Runs mutation tests with infection
	mkdir -p .build/infection
	vendor/bin/infection --ignore-msi-with-no-mutations --min-covered-msi=100 --min-msi=100

stan: vendor ## Runs a static analysis with phpstan
	mkdir -p .build/phpstan
	vendor/bin/phpstan analyse --configuration=phpstan.neon

test: vendor ## Runs auto-review, unit, and integration tests with phpunit
	mkdir -p .build/phpunit
	vendor/bin/phpunit

vendor: composer.json composer.lock
	composer validate --strict
	composer install
	composer normalize
