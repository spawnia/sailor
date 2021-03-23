<div align="center">
  <img src="sailor.png" alt=sailor-logo">
</div>

<div align="center">

[![CI Status](https://github.com/spawnia/sailor/workflows/Continuous%20Integration/badge.svg)](https://github.com/spawnia/sailor/actions)
[![codecov](https://codecov.io/gh/spawnia/sailor/branch/master/graph/badge.svg)](https://codecov.io/gh/spawnia/sailor)
[![StyleCI](https://github.styleci.io/repos/207396174/shield?branch=master)](https://github.styleci.io/repos/207396174)
[![Latest Stable Version](https://poser.pugx.org/spawnia/sailor/v/stable)](https://packagist.org/packages/spawnia/sailor)
[![Total Downloads](https://poser.pugx.org/spawnia/sailor/downloads)](https://packagist.org/packages/spawnia/sailor)

A typesafe GraphQL client for PHP

</div>

## Motivation

GraphQL provides typesafe API access through the schema definition each
server provides through introspection. Sailor leverages that information
to enable an ergonomic workflow and reduce type-related bugs in your code.

The native GraphQL query language is the most universally used tool to formulate
GraphQL queries and works natively with the entire ecosystem of GraphQL tools.
Sailor takes the plain queries you write and generates executable PHP code,
using the server schema to generate type safe operations and results.

## Installation

Install Sailor through composer by running:

    composer require spawnia/sailor

## Configuration

Run `vendor/bin/sailor` to set up the configuration.
A file called `sailor.php` will be created in your project root.

You can take a look at the example configuration to see what options
are available for configuration: [`sailor.php`](sailor.php).

It is quite useful to include dynamic values in your configuration.
You might use [PHP dotenv](https://github.com/vlucas/phpdotenv) to load
environment variables (run `composer require vlucas/phpdotenv` if you do not have it installed already.).

```diff
# sailor.php
+$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
+$dotenv->load();

...
        public function makeClient(): Client
        {
            return new \Spawnia\Sailor\Client\Guzzle(
-               'http://hardcoded.url/',
+               getenv('EXAMPLE_API_URL'),
                [
                    'headers' => [
-                       'Authorization' => 'hardcoded-api-token',
+                       'Authorization' => getenv('EXAMPLE_API_TOKEN'),
                    ],
                ]
            );
        }
```

## Usage

### Introspection

Run `vendor/bin/sailor introspect` to update your schema with the latest changes
from the server by running an introspection query. As an example, a very simple
server might result in the following file being placed in your project:

```graphql
# schema.graphqls
type Query {
  hello(name: String): String
}
```

### Define operations

Put your queries and mutations into `.graphql` files and place them anywhere within your
configured project directory. You are free to name and structure the files in any way.
Let's query the example schema from above:

```graphql
# src/example.graphql
query HelloSailor {
  hello(name: "Sailor")
}
```

The only requirement is that you must give all your operations unique names.

```graphql
# Invalid, operations have to be named
query {
  anonymous
}

# Invalid, names must be unique across all operations
query Foo { ... }
mutation Foo { ... }
```

### Generate code

Run `vendor/bin/sailor` to generate PHP code for your operations.
For the example above, Sailor will generate a class called `HelloSailor`,
place it in the configured namespace and write it to the configured location.

```php
<?php

declare(strict_types=1);

namespace Example\Api;

class HelloSailor extends \Spawnia\Sailor\Operation { ... }
```

There are additional generated classes that represent the results of calling
the operations. The plain data from the server is wrapped up and contained
within those value classes so you can access them in a typesafe way.

### Execute queries

You are now set up to run a query
against the server, just call the `execute()` function of the new query class:

```php
$result = \Example\Api\HelloSailor::execute();
```

The returned `$result` is going to be a class that extends `\Spawnia\Sailor\Result` and
holds the decoded response returned from the server. You can just grab the `$data`, `$errors`
or `$extensions` off of it:

```php
$result->data        // `null` or a generated subclass of `\Spawnia\Sailor\TypedObject`
$result->errors      // `null` or a list of errors
$result->extensions  // `null` or an arbitrary map
```

You can ensure your query returned the proper data and contained no errors:

```php
$errorFreeResult = $result->errorFree(); // Throws if there are errors
```

## Testing

Sailor provides first class support for testing by allowing you to mock operations.

### Setup

It is assumed you are using [PHPUnit](https://phpunit.de/) and [Mockery](http://docs.mockery.io/en/latest/).

    composer require --dev phpunit/phpunit mockery/mockery

Make sure your test class - or one of its parents - uses the following traits:

```php
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Spawnia\Sailor\Testing\UsesSailorMocks;

abstract class TestCase extends PHPUnitTestCase
{
    use MockeryPHPUnitIntegration;
    use UsesSailorMocks;
}
```

### Mock results

Mocks are registered per operation class:

```php
/** @var \Mockery\MockInterface&\Example\Api\HelloSailor */
$mock = \Example\Api\HelloSailor::mock();
```

Now, any subsequent calls to `HelloSailor::execute()` will be passed on to `$mock`.
Use it to build up expectations for what calls it should receive and mock returned results:

```php
$mock
    ->expects('execute')
    ->once()
    ->with('Sailor')
    ->andReturn(HelloSailorResult::fromStdClass((object) [
        'data' => (object) [
            'hello' => 'Hello, Sailor!',
        ],
    ]));

self::assertSame(
    'Hello, Sailor!',
    HelloSailor::execute('Sailor')->data->hello
);
```

## Examples

You can find examples of how a project would use Sailor within [examples](examples).

## Changelog

See [`CHANGELOG.md`](CHANGELOG.md).

## Contributing

See [`CONTRIBUTING.md`](CONTRIBUTING.md).

## License

This package is licensed using the MIT License.
