# CONTRIBUTING

We are using [GitHub Actions](https://github.com/features/actions) as a continuous integration system.

For details, see [`workflows/continuous-integration.yml`](workflows/continuous-integration.yml).

## Setup

Make sure you have `PHP`, `composer` and `make` installed, as well as:

    composer global require ergebnis/composer-normalize

## Code Style

The code style is automatically fixed through [StyleCI](https://styleci.io/).

## Commands

Run the following  to display a list of available targets with corresponding descriptions:

    make help

## Code guidelines

### `protected` over `private`

Always use class member visibility `protected` over `private`. We cannot foresee every
possible use case in advance, extending the code should remain possible.
