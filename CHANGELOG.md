# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased

### Added

- Support interfaces

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
