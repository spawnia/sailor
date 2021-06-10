# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased

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
