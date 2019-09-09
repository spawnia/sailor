# CONTRIBUTING

We are using [GitHub Actions](https://github.com/features/actions) as a continuous integration system.

For details, see [`workflows/continuous-integration.yml`](workflows/continuous-integration.yml).

## Code Style

The code style is automatically fixed through [StyleCI](https://styleci.io/).

## Static Code Analysis

We are using [`phpstan/phpstan`](https://github.com/phpstan/phpstan) to statically analyze the code.

Run

```bash
make stan
```

to run a static code analysis.

## Tests

We are using [`phpunit/phpunit`](https://github.com/sebastianbergmann/phpunit) to drive the development.

Run

```bash
make test
```

to run all the tests.

## Mutation Tests

We are using [`infection/infection`](https://github.com/infection/infection) to ensure a minimum quality of the tests.

Enable `Xdebug` and run

```bash
make infection
```

to run mutation tests.

## Extra lazy?

Run

```bash
make
```

to enforce coding standards, perform a static code analysis, and run tests!

:bulb: Run

```bash
make help
```

to display a list of available targets with corresponding descriptions.
