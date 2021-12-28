.PHONY: it
it: fix stan test ## Run the commonly used targets

.PHONY: help
help: ## Displays this list of targets with descriptions
	@grep -E '^[a-zA-Z0-9_-]+:.*?## .*$$' $(firstword $(MAKEFILE_LIST)) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: fix
fix: vendor
	vendor/bin/php-cs-fixer fix

.PHONY: stan
stan: ## Runs static analysis with phpstan
	mkdir -p .build/phpstan
	vendor/bin/phpstan analyse --configuration=phpstan.neon

.PHONY: codegen
codegen: ## Runs the codegen tests
	mkdir -p .build/phpunit
	vendor/bin/phpunit --filter CodegenTest

.PHONY: test
test: ## Runs tests with phpunit
	mkdir -p .build/phpunit
	vendor/bin/phpunit

.PHONY: coverage
coverage: ## Collects coverage from running unit tests with phpunit
	mkdir -p .build/phpunit
	vendor/bin/phpunit --dump-xdebug-filter=.build/phpunit/xdebug-filter.php
	vendor/bin/phpunit --coverage-text --prepend=.build/phpunit/xdebug-filter.php

.PHONY: infection
infection: ## Runs mutation tests with infection
	mkdir -p .build/infection
	vendor/bin/infection --ignore-msi-with-no-mutations --min-covered-msi=100 --min-msi=100

define approve_example
	rm -rf examples/$(1)/expected
	cp -r examples/$(1)/generated examples/$(1)/expected
endef

.PHONY: approve
approve: ## Accept the current generated code as expected
	$(call approve_example,custom-types)
	$(call approve_example,input)
	$(call approve_example,polymorphic)
	$(call approve_example,simple)

vendor: composer.json composer.lock
	composer install
	composer validate
	composer normalize

composer.lock: composer.json
	composer update
