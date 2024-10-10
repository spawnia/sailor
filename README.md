<div align="center">
  <img src="sailor.png" alt=sailor-logo">
</div>

<div align="center">

[![CI Status](https://github.com/spawnia/sailor/workflows/Validate/badge.svg)](https://github.com/spawnia/sailor/actions)
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

## Installation

Install Sailor through composer by running:

    composer require spawnia/sailor

If you want to use the built-in default Client (see [Client implementations](#client-implementations)):

    composer require guzzlehttp/guzzle

If you want to use the PSR-18 Client and don't have
PSR-17 Request and Stream factory implementations (see [Client implementations](#client-implementations)):

    composer require nyholm/psr7

## Configuration

Run `vendor/bin/sailor` to set up the configuration.
A file called `sailor.php` will be created in your project root.

A Sailor configuration file is expected to return an associative array
where the keys are endpoint names and the values are instances of `Spawnia\Sailor\EndpointConfig`.

You can take a look at the example configuration to see what options
are available for configuration: [`sailor.php`](sailor.php).

If you would like to use multiple configuration files,
specify which file to use through the `-c/--config` option.

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

Custom scalars are commonly serialized as strings, but may also use other representations.
Without knowing about the contents of the type, Sailor can not do any conversions or provide more accurate type hints, so it uses `mixed`.

Enums are only supported from PHP 8.1. Many projects simply used scalar values or an implementation
that approximates enums through some kind of value class. Sailor is not opinionated and generates
enums as a class with string constants and does no conversion - useful but not perfect.
For an improved experience, it is recommended to customize the enum generation/conversion.

Overwrite `EndpointConfig::configureTypes()` to specialize how Sailor deals with the types within your schema.
See [examples/custom-types](examples/custom-types).

### Error conversion

Errors sent within the GraphQL response must follow the [response errors specification](http://spec.graphql.org/October2021/#sec-Errors).
Sailor converts the plain `stdClass` obtained from decoding the JSON response into
instances of `\Spawnia\Sailor\Error\Error` by default.

If one of your endpoints returns structured data in `extensions`, you can customize how
the plain errors are decoded into class instances by overwriting `EndpointConfig::parseError()`.

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

You must give all your operations unique `PascalCase` names, the following example is invalid:

```graphql
# Invalid, operations have to be named
query {
  anonymous
}

# Invalid, names must be unique across all operations
query Foo { ... }
mutation Foo { ... }

# Invalid, names must be PascalCase
query camelCase { ... }
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
holds the decoded response returned from the server.
You can just grab the `$data`, `$errors` or `$extensions` off of it:

```php
$result->data       // `null` or a generated subclass of `\Spawnia\Sailor\ObjectLike`
$result->errors     // `null` or a list of `\Spawnia\Sailor\Error\Error`
$result->extensions // `null` or an arbitrary map
```

### Error handling

You can ensure an operation returned the proper data and contained no errors:

```php
$errorFreeResult = \Example\Api\Operations\HelloSailor::execute()
    ->errorFree(); // Throws if there are errors or returns an error free result
```

The `$errorFreeResult` is going to be a class that extends `\Spawnia\Sailor\ErrorFreeResult`.
Given it can only be obtained by going through validation,
it is guaranteed to have non-null `$data` and does not have `$errors`:

```php
$errorFreeResult->data       // a generated subclass of `\Spawnia\Sailor\ObjectLike`
$errorFreeResult->extensions // `null` or an arbitrary map
```

If you do not need to access the data and just want to ensure a mutation was successful,
the following is more efficient as it does not instantiate a new object:

```php
\Example\Api\Operations\SomeMutation::execute()
    ->assertErrorFree(); // Throws if there are errors
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
  requiredID: Int!,
  firstOptional: Int,
  secondOptional: Int,
}
```

Suppose we allow instantiation in PHP with the following implementation:

```php
class SomeInput extends \Spawnia\Sailor\ObjectLike
{
    public static function make(
        int $requiredID,
        ?int $firstOptional = null,
        ?int $secondOptional = null,
    ): self {
        $instance = new self;

        $instance->requiredID = $required;
        $instance->firstOptional = $firstOptional;
        $instance->secondOptional = $secondOptional;

        return $instance;
    }
}
```

Given that implementation, the following call will produce the following JSON payload:

```php
SomeInput::make(requiredID: 1, secondOptional: 2);
```

```json
{ "requiredID": 1, "firstOptional": null, "secondOptional": 2 }
```

However, we would like to produce the following JSON payload:

```json
{ "requiredID": 1, "secondOptional": 2 }
```

This is because from within `make()`, there is no way to differentiate between an explicitly
passed optional named argument and one that has been assigned the default value.
Thus, the resulting JSON payload will unintentionally modify `firstOptional` too,
erasing whatever value it previously held.

A naive solution to this would be to filter out any argument that is `null`.
However, we would also like to be able to explicitly set the first optional value to `null`.
The following call *should* result in a JSON payload that contains `"firstOptional": null`.

```php
SomeInput::make(requiredID: 1, firstOptional: null, secondOptional: 2);
```

In order to generate partial inputs by default, optional named arguments have a special default value:

```php
Spawnia\Sailor\ObjectLike::UNDEFINED = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.';
```

```php
class SomeInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int $requiredID
     * @param int|null $firstOptional
     * @param int|null $secondOptional
     */
    public static function make(
        $requiredID,
        $firstOptional = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $secondOptional = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($requiredID !== self::UNDEFINED) {
            $instance->requiredID = $requiredID;
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

You may use `Spawnia\Sailor\ObjectLike::UNDEFINED` to omit nullable arguments completely:

```php
SomeInput::make(
    requiredID: 1,
    firstOptional: $maybeNull ?? Spawnia\Sailor\ObjectLike::UNDEFINED,
);
```

If `$maybeNull` is `null`, this will result in the following JSON payload:

```json
{ "requiredID": 1 }
```

In the very unlikely case where you need to pass exactly the value of `Spawnia\Sailor\ObjectLike::UNDEFINED`,
you can bypass the logic in `make()` and assign it directly:

```php
$input = SomeInput::make(requiredID: 1);
$input->secondOptional = Spawnia\Sailor\ObjectLike::UNDEFINED;
```

### Events

Sailor calls `EndpointConfig::handleEvent()` with the following events during the execution lifecycle:

1. [StartRequest](src/Events/StartRequest.php): Fired after calling `execute()` on an `Operation`, before invoking the client.
2. [ReceiveResponse](src/Events/ReceiveResponse.php): Fired after receiving a GraphQL response from the client.

### PHP keyword collisions

Since GraphQL uses a different set of reserved keywords, names of fields or types may collide with PHP keywords.
Sailor prevents illegal usages of those names in generated code by prefixing them with a single underscore `_`.

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
use Example\Api\Operations\HelloSailor;

/** @var \Mockery\MockInterface&HelloSailor */
$mock = HelloSailor::mock();
```

When registered, the mock captures all calls to `HelloSailor::execute()`.
Use it to build up expectations for what calls it should receive and mock returned results:

```php
$hello = 'Hello, Sailor!';

$mock
    ->expects('execute')
    ->once()
    ->with('Sailor')
    ->andReturn(HelloSailor\HelloSailorResult::fromData(
        HelloSailor\HelloSailor::make($hello),
    ));

$result = HelloSailor::execute('Sailor')->errorFree();

self::assertSame($hello, $result->data->hello);
```

Subsequent calls to `::mock()` will return the initially registered mock instance.

```php
$mock1 = HelloSailor::mock();
$mock2 = HelloSailor::mock();
assert($mock1 === $mock2); // true
```

You can also simulate a result with errors:

```php
HelloSailor\HelloSailorResult::fromErrors([
    (object) [
        'message' => 'Something went wrong',
    ],
]);
```

For PHP 8 users, it is recommended to use named arguments to build complex mocked results:

```php
HelloSailor\HelloSailorResult::fromData(
    HelloSailor\HelloSailor::make(
        hello: 'Hello, Sailor!',
        nested: HelloSailor\HelloSailor\Nested::make(
            hello: 'Hello again!',
        ),
    ),
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
