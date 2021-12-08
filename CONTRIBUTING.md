# CONTRIBUTING

We are using [GitHub Actions](https://github.com/features/actions) as a continuous integration system.

For details, see [`workflows/continuous-integration.yml`](workflows/continuous-integration.yml).

## Setup

Make sure you have `PHP`, `composer` and `make` installed.

## Code Style

We are using [`friendsofphp/php-cs-fixer`](https://github.com/friendsofphp/php-cs-fixer) to automatically format the code.

## Commands

Run the following  to display a list of available targets with corresponding descriptions:

    make help

## Code guidelines

### `protected` over `private`

Always use class member visibility `protected` over `private`. We cannot foresee every
possible use case in advance, extending the code should remain possible.
