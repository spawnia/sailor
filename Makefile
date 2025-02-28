.PHONY: it
it: fix stan approve test test-examples ## Run the commonly used targets

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

.PHONY: test-examples
test-examples: ## Test examples
	cd examples/custom-types && ./test.sh
	cd examples/input && ./test.sh
	cd examples/install && ./test.sh
	cd examples/php-keywords && ./test.sh
	cd examples/polymorphic && ./test.sh
	cd examples/simple && ./test.sh

vendor: composer.json composer.lock
	composer install
	composer validate
	composer normalize

composer.lock: composer.json
	composer update
