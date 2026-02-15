.PHONY: it
it: fix stan approve test validate-examples ## Run the commonly used targets

.PHONY: help
help: ## Displays this list of targets with descriptions
	@grep --extended-regexp '^[a-zA-Z0-9_-]+:.*?## .*$$' $(firstword $(MAKEFILE_LIST)) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: fix
fix: vendor
	vendor/bin/php-cs-fixer fix

.PHONY: stan
stan: ## Runs static analysis with phpstan
	mkdir --parents .build/phpstan
	vendor/bin/phpstan analyse --configuration=phpstan.neon

.PHONY: test
test: ## Runs tests with phpunit
	mkdir --parents .build/phpunit
	vendor/bin/phpunit

.PHONY: coverage
coverage: ## Collects coverage from running unit tests with phpunit
	mkdir --parents .build/phpunit
	vendor/bin/phpunit --dump-xdebug-filter=.build/phpunit/xdebug-filter.php
	vendor/bin/phpunit --coverage-text --prepend=.build/phpunit/xdebug-filter.php

.PHONY: approve
approve: ## Generate code and approve it as expected
	tests/generate-and-approve-examples.php

.PHONY: validate-examples
validate-examples: ## Run integration tests on examples
	examples/validate.sh custom-types --dependencies=highest
	examples/validate.sh inline-fragments --dependencies=highest
	examples/validate.sh input --dependencies=highest
	examples/validate.sh install --dependencies=highest
	examples/validate.sh php-keywords --dependencies=highest
	examples/validate.sh polymorphic --dependencies=highest
	examples/validate.sh simple --dependencies=highest

vendor: composer.json composer.lock
	composer install
	composer validate
	composer normalize

composer.lock: composer.json
	composer update
