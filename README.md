<div align="center">
  <img src="sailor.png" alt=sailor-logo">
</div>

<div align="center">

[![CI Status](https://github.com/spawnia/sailor/workflows/Continuous%20Integration/badge.svg)](https://github.com/spawnia/sailor/actions)
[![codecov](https://codecov.io/gh/spawnia/sailor/branch/master/graph/badge.svg)](https://codecov.io/gh/spawnia/sailor)

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
using the server schema to generate typesafe operations and results.

## Missing features

Sailor does not support the following essential GraphQL features yet:
- [Fragments](https://github.com/spawnia/sailor/issues/7)

## Installation

Install Sailor through composer by running:

    composer require spawnia/sailor

If you want to use the built-in default Client (see [Client implementations](#client-implementations)):

    composer require guzzle/guzzle

If you want to use the PSR-18 Client and don't have
PSR-17 Request and Stream factory implementations (see [Client implementations](#client-implementations)):

    composer require nyholm/psr7

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
-               'https://hardcoded.url',
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

### Client implementations

Sailor provides a few built-in clients:
- `Spawnia\Sailor\Client\Guzzle`: Default HTTP client
- `Spawnia\Sailor\Client\Psr18`: PSR-18 HTTP client
- `Spawnia\Sailor\Client\Log`: Used for testing

You can bring your own by implementing the interface `Spawnia\Sailor\Client`.

### Dynamic clients

You can configure clients dynamically for specific operations or per request:

```php
use Example\Api\Operations\HelloSailor;

/** @var \Spawnia\Sailor\Client $client Somehow instantiated dynamically */

HelloSailor::setClient($client);

// Will use $client over the client from EndpointConfig
$result = HelloSailor::execute();

// Reverts to using the client from EndpointConfig
HelloSailor::setClient(null);
```

### Custom types

You may overwrite `EndpointConfig::configureTypes()` to specialize the configuration
for how Sailor deals with the types within your schema. The main use case for this
is custom scalar types and custom enum types. See [examples/custom-types](examples/custom-types).

## Usage

### Introspection

Run `vendor/bin/sailor introspect` to update your schema with the latest changes
from the server by running an introspection query. As an example, a very simple
server might result in the following file being placed in your project:

```graphql
# schema.graphql
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

You must give all your operations unique names, the following example is invalid:

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
namespace Example\Api\Operations;

class HelloSailor extends \Spawnia\Sailor\Operation { ... }
```

There are additional generated classes that represent the results of calling
the operations. The plain data from the server is wrapped up and contained
within those value classes, so you can access them in a typesafe way.

### Execute queries

You are now set up to run a query against the server,
just call the `execute()` function of the new query class:

```php
$result = \Example\Api\Operations\HelloSailor::execute();
```

The returned `$result` is going to be a class that extends `\Spawnia\Sailor\Result` and
holds the decoded response returned from the server. You can just grab the `$data`, `$errors`
or `$extensions` off of it:

```php
$result->data        // `null` or a generated subclass of `\Spawnia\Sailor\ObjectLike`
$result->errors      // `null` or a list of errors
$result->extensions  // `null` or an arbitrary map
```

You can ensure your query returned the proper data and contained no errors:

```php
$errorFreeResult = $result->errorFree(); // Throws if there are errors
```

### Queries with arguments

Your generated operation classes will be annotated with the arguments your query defines.

```php
class HelloSailor extends \Spawnia\Sailor\Operation
{
    public static function execute(string $required, ?\Example\Api\Types\SomeInput $input = null): HelloSailor\HelloSailorResult { ... }
}
```

Inputs can be built up incrementally:

```php
$input = new \Example\Api\Types\SomeInput;
$input->foo = 'bar';
```

If you are using PHP 8, instantiation with named arguments can be quite useful to ensure your
input is completely filled:

```php
\Example\Api\Types\SomeInput::make(foo: 'bar')
```

### Partial inputs

GraphQL often uses a pattern of partial inputs - the equivalent of an HTTP `PATCH`.
Consider the following input:

```graphql
input SomeInput {
  requiredId: Int!,
  firstOptional: Int,
  secondOptional: Int,
}
```

Suppose we allow instantiation in PHP with the following implementation:

```php
class SomeInput extends \Spawnia\Sailor\ObjectLike
{
    public static function make(
        int $requiredId,
        ?int $firstOptional = null,
        ?int $secondOptional = null,
    ): self {
        $instance = new self;

        $instance->requiredId = $required;
        $instance->firstOptional = $firstOptional;
        $instance->secondOptional = $secondOptional;

        return $instance;
    }
}
```

The following call:

```php
SomeInput::make(requiredId: 1, secondOptional: 2);
```

Should produce the following JSON payload:

```json
{ "requiredId": 1, "secondOptional": 2 }
```

However, from within `make()` there is no way to differentiate between an explicitly
passed optional named argument and one that has been assigned the default value.
Thus, the resulting JSON payload will unintentionally modify `firstOptional` too, erasing
whatever value it previously held.

```json
{ "requiredId": 1, "firstOptional": null, "secondOptional": 2 }
```

A naive solution to this would be to filter out any argument that is `null`.
However, we would also like to be able to explicitly set the first optional value to `null`.
The following call *should* result in the previous JSON payload.

```php
SomeInput::make(requiredId: 1, firstOptional: null, secondOptional: 2);
```

In order to generate partial inputs by default, optional named arguments have a special default value
of `PHP_FLOAT_MAX - 1` which is equivalent to `1.7976931348623157E+308`. This allows Sailor to differentiate
between explicitly passing `null` and not passing a value at all.

```php
class SomeInput extends \Spawnia\Sailor\ObjectLike
{
    const UNDEFINED = PHP_FLOAT_MAX - 1;

    /**
     * @param int $requiredId
     * @param int|null $firstOptional
     * @param int|null $secondOptional
     */
    public static function make(
        $requiredId,
        $firstOptional = self::UNDEFINED,
        $secondOptional = self::UNDEFINED,
    ): self {
        $instance = new self;

        if ($requiredId !== self::UNDEFINED) {
            $instance->requiredId = $requiredId;
        }
        if ($firstOptional !== self::UNDEFINED) {
            $instance->firstOptional = $firstOptional;
        }
        if ($secondOptional !== self::UNDEFINED) {
            $instance->secondOptional = $secondOptional;
        }

        return $instance;
    }
}
```

In the unlikely case where you need to pass a float value that might be close to or exactly match
the special value, you can assign it directly:

```php
$input = SomeInput::make(requiredId: 1);
$input->secondOptional = $someFloat;
```

## Testing

Sailor provides first class support for testing by allowing you to mock operations.

### Setup

It is assumed you are using [PHPUnit](https://phpunit.de) and [Mockery](https://docs.mockery.io/en/latest).

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

Otherwise, mocks are not reset between test methods, you might run into very confusing bugs.

### Mock results

Mocks are registered per operation class:

```php
/** @var \Mockery\MockInterface&\Example\Api\Operations\HelloSailor */
$mock = \Example\Api\Operations\HelloSailor::mock();
```

When registered, the mock captures all calls to `HelloSailor::execute()`.
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

$result = HelloSailor::execute('Sailor')->errorFree();

self::assertSame('Hello, Sailor!', $result->data->hello);
```

Subsequent calls to `::mock()` will return the initially registered mock instance.

```php
$mock1 = HelloSailor::mock();
$mock2 = HelloSailor::mock();
assert($mock1 === $mock2); // true
```

You can also simulate a result with errors:

```php
HelloSailorResult::fromErrors([
    (object) [
        'message' => 'Something went wrong',
    ],
]);
```

For PHP 8 users, there is a more ergonomic method of instantiating mocked results:

```php
HelloSailorResult::fromData(
    HelloSailor::make(
        hello: 'Hello, Sailor!',
        nested: HelloSailor\Nested::make(
            hello: 'Hello again!',
        )
    )
))
```

### Integration

If you want to perform integration testing for a service that uses Sailor without actually
hitting an external API, you can swap out your client with the `Log` client.
It writes all requests made through Sailor to a file of your choice.

> The `Log` client can not know what constitutes a valid response for a given request,
> so it always responds with an error.

```php
# sailor.php
public function makeClient(): Client
{
    return new \Spawnia\Sailor\Client\Log(__DIR__ . '/sailor-requests.log');
}
```

Each request goes on a new line and contains a JSON string that holds the `query` and `variables`:

```json
{"query":"{ foo }","variables":{"bar":42}}
{"query":"mutation { baz }","variables":null}
```

This allows you to perform assertions on the calls that were made.
The `Log` client offers a convenient method of reading the requests as structured data:

```php
$log = new \Spawnia\Sailor\Client\Log(__DIR__ . '/sailor-requests.log');
foreach ($log->requests() as $request) {
    var_dump($request);
}

array(2) {
  ["query"]=>
  string(7) "{ foo }"
  ["variables"]=>
  array(1) {
    ["bar"]=>
    int(42)
  }
}
array(2) {
  ["query"]=>
  string(7) "mutation { baz }"
  ["variables"]=>
  NULL
}
```

To clean up the log after performing tests, use `Log::clear()`.

## Examples

You can find examples of how a project would use Sailor within [examples](examples).

## Changelog

See [`CHANGELOG.md`](CHANGELOG.md).

## Contributing

See [`CONTRIBUTING.md`](CONTRIBUTING.md).

## License

This package is licensed using the MIT License.
