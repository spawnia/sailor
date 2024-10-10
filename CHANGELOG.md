# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

[See releases on GitHub](https://github.com/spawnia/sailor/releases).

## Unreleased

## v0.34.0

### Changed

- Make `Result::assertErrorFree()` return void

## v0.33.1

### Fixed

- Escape class names of result types named after PHP reserved keywords

## v0.33.0

### Changed

- Validate operation names start with upper case characters

## v0.32.2

### Changed

- Improve configuration documentation

## v0.32.1

### Added

- Include `extensions.debugMessage` in errors, if present

## v0.32.0

### Changed

- Remove `EndpointConfig::searchPath()`, require `finder()` instead

## v0.31.2

### Added

- Add `DirectoriesFinder`

## v0.31.1

### Added

- Use PHPUnit 11 attributes for testing helpers

## v0.31.0

### Fixed

- Allow `symfony/var-exporter:^7`

## v0.30.1

### Fixed

- Fix registering commands with symfony/console v7

## v0.30.0

### Added

- Allow `symfony/console:^7`

## v0.29.3

### Fixed

- Fix namespace printing with `nette/php-generator:^4.1.1`

## v0.29.2

### Fixed

- Do not assume scalar values are `string`, use `mixed`

## v0.29.1

### Fixed

- Default to `null` when explicitly accessing undefined optional input properties

## v0.29.0

### Added

- Allow `EndpointConfig::configureTypes()` to customize object, interface and union type code generation
- Add `Operation::clearClients()` and `ClearsSailorClients`

### Changed

- Only instantiate client once per operation
- Expect `callable $request` in `MockClient` constructor over pushing to `$responseMocks`

## v0.28.2

### Fixed

- Clear log file when instantiating `Log`

## v0.28.1

### Fixed

- Handle queries with explicit `__typename` in fragments

## v0.28.0

### Added

- Support `nette/php-generator:^4`

## v0.27.0

### Added

- Support `webonyx/graphql-php:^15`

## v0.26.1

### Fixed

- Include client directives when inlining fragments

## v0.26.0

### Added

- Accept `int` in arguments of type `ID` and `Float`

### Changed

- Split `TypeConfig::typeReference()` into `InputTypeConfig::inputTypeReference()` and `OutputTypeConfig::outputTypeReference()`

## v0.25.0

### Added

- Implement `PolymorphicConverter::toGraphQL()`

## v0.24.0

### Added

- Accept any array as inputs and serialize it as a list

## v0.23.1

### Fixed

- Normalize path to config file

## v0.23.0

### Added

- Pass events `StartRequest` and `ReceiveResponse` to `EndpointConfig::handleEvent()`

## v0.22.0

### Added

- Add support for types and fields named equivalent to PHP keywords like `type Switch` or `{ print { subfield } }`

### Removed

- No longer generate class `TypeConverters`

## v0.21.2

### Fixed

- Always create log file when instantiating `Log` client

## v0.21.1

### Changed

- Improve validation error for missing fields or required data

### Fixed

- Add correct `__typename` in polymorphic types `make()` method

## v0.21.0

### Added

- Allow specifying `--configuration` option to CLI commands to read specific config file

## v0.20.2

### Changed

- Ignore the schema itself when looking for operations

## v0.20.1

### Changed

- Simply ignore non-executable definitions

## v0.20.0

### Added

- Support fragments

### Fixed

- Merge fields between diverging subtrees within inline fragments

## v0.19.0

### Added

- Allow customizing how documents are found

## v0.18.2

### Fixed

- Validate endpoint names in `introspect` command are strings

## v0.18.1

### Fixed

- Fix PHP 8.1 compatibility

## v0.18.0

### Added

- Allow `thecodingmachine/safe` v2 as dependency

## v0.17.1

### Fixed

- Set errors property of `Spawnia\Sailor\Error\ResultErrorsException`

## v0.17.0

### Changed

- Use self-explanatory string value for `ObjectLike::UNDEFINED`

## v0.16.0

### Added

- Add configuration `EndpointConfig::errorsAreClientSafe()` to propagate client-safety of endpoint errors

### Changed

- Move `Spawnia\Sailor\ResultErrorsException` to `Spawnia\Sailor\Error\ResultErrorsException`
- Move `Spawnia\Sailor\InvalidDataException` to `Spawnia\Sailor\Error\InvalidDataException`
- Include only messages in `ResultErrorsException::$message`, expose full `Error` objects as `ResultErrorsException::$errors`

### Removed

- Remove `Spawnia\Sailor\Response::assertErrorFree()`

## v0.15.0

### Added

- Add ability to overwrite parsing of errors

### Changed

- Convert errors from plain `stdClass` to `Spawnia\Sailor\Error\Error` in results

## v0.14.1

### Fixed

- Fix conversion of custom types used directly in variables

## v0.14.0

### Changed

- Generate operations under namespace `Operations`
- Generate enums and inputs under namespace `Types`
- Base inputs and results on class `ObjectLike`

### Added

- Allow customization of how Sailor deals with types using `EndpointConfig::configureTypes()`
- Allow additional code generation with `EndpointConfig::generateClasses()`
- Ease mock result instantiation with `Result::fromData()` and `Result::fromErrors()`
- Ease input and mock data instantiation with `ObjectLike::make()`

## v0.13.0

### Added

- Allow `symfony/console` and `symfony/var-exporter` v6 as dependencies

## v0.12.0

### Added

- Generate Enums and Inputs from the schema

### Changed

- Operations with input object types as arguments expect generated classes instead of `\stdClass`
- The operation names `Inputs` and `Enums` are now reserved

## v0.11.0

### Added

- Support interfaces
- Support unions
- Ensure generated code complies with PHPStan v1 at level max

### Changed

- Always add field `__typename` to any subselection and have it available in the result through `TypedObject`
- Name generated `TypedObject` classes after the corresponding object type

## v0.10.2

### Fixed

- Fall back to not querying `Directive.isRepeatable` if it is not available

## v0.10.1

### Fixed

- Allow `"nette/php-generator": "^3.6.3"`

## v0.10.0

### Added

- Allow overwriting the client from EndpointConfig for specific operations or per request

## v0.9.0

### Added

- Add PSR-18 client https://github.com/spawnia/sailor/pull/28

### Changed

- `Response::fromResponseInterface()` no longer accepts non-200 status codes

## v0.8.0

### Changed

- Remove forced dependency on `guzzle/guzzle`

## v0.7.0

### Added

- Add methods `Log::requests()` and `Log::clear()` to ease assertions on `Log` client https://github.com/spawnia/sailor/pull/25

## v0.6.0

### Added

- Add `Log` client for integration testing https://github.com/spawnia/sailor/pull/24

## v0.5.0

### Changed

- Return the first registered mock instance for each operation class
  on subsequent calls to `Operation::mock()`

## v0.4.2

### Fixed

- Fix initialization of Result from Response

## v0.4.1

### Changed

- Improve error message when trying to map unknown fields onto result classes

## v0.4.0

### Added

- Add method `Result::errorFree()` to ease safe access to a result class without errors

## v0.3.0

### Added

- Allow mocking single operations with Mockery

### Fixed

- Fix codegen for operations with multiple parameters

### Changed

- Require `Directive.isRepeatable` on introspection
- Use typed properties
- Rewrite `Configuration` class

### Removed

- Remove `EndpointConfig::$mockClient` and `EndpointConfig::client()`

## v0.2.0

### Added

- Support PHP 8

### Changed

- Require PHP 7.4 or 8
- Require newer versions of various dependencies

## v0.1.0

### Added

- Fetch introspection results via `vendor/bin/sailor introspect`
- Define operations in `.graphql` files and automatically generate client code
- Statically validate correctness of defined operations
