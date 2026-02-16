# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Sailor is a typesafe GraphQL client for PHP. It generates PHP classes from GraphQL schema and `.graphql` operation files, enabling fully typed GraphQL API interactions.

## Commands

```bash
make it                  # Run fix, stan, approve, test, validate-examples (the full suite)
make fix                 # Auto-fix code style (php-cs-fixer)
make stan                # Static analysis (PHPStan, level max + bleedingEdge)
make test                # Run all tests (PHPUnit)
make approve             # Generate code for examples and approve as expected output
make validate-examples   # Run integration tests on all examples
make coverage            # Run tests with coverage
```

**Running a single test:**
```bash
vendor/bin/phpunit tests/Unit/ResponseTest.php
vendor/bin/phpunit --filter testMethodName
```

**Test suites:** `Unit`, `Integration` (run with `--testsuite`)

## Architecture

**Code generation flow:** `Console/CodegenCommand` → `Codegen/Generator` → reads schema + `.graphql` files → validates → `OperationBuilder`/`ObjectLikeBuilder` generate classes → `Writer` outputs PHP files.

**Runtime flow:** `MyOperation::execute($args)` → `Operation::executeOperation()` → `Client` sends HTTP request → `Response` parsed into typed `Result` object.

**Key base classes in `src/`:**
- `EndpointConfig` — abstract configuration per GraphQL endpoint
- `Operation` — base for all generated operation classes
- `ObjectLike` — base for generated types/inputs, uses `__get`/`__set` with property validation
- `Result` — typed operation result wrapper
- `Client` — interface for HTTP clients (implementations: `Guzzle`, `Psr18`, `Log`)

## Examples as Golden-File Tests

The `examples/` directory (e.g., `simple/`, `polymorphic/`, `custom-types/`) serves triple duty: test fixtures, working documentation, and integration tests. Each example is a self-contained project with:

- `sailor.php` — endpoint configuration
- `schema.graphql` — GraphQL schema
- `src/` — `.graphql` operation files (input)
- `expected/` — approved generated PHP code (committed to git, the golden files)
- `generated/` — freshly generated output (gitignored, ephemeral)
- `test.php` — integration test using the generated code

**CodegenTest** (`tests/Integration/CodegenTest.php`) is the critical test: it regenerates code for each example into `generated/`, then asserts every file matches `expected/` exactly. Any change to codegen logic causes this test to fail, making regressions immediately visible.

**Workflow after changing codegen:**
1. `make test` — CodegenTest fails, showing diffs against expected output
2. Inspect diffs to verify correctness
3. `make approve` — regenerates all examples and copies `generated/` → `expected/`, blessing the new output
4. Commit both code changes and updated `expected/` files

**`make validate-examples`** complements CodegenTest by running each example end-to-end as a real project: `composer install` → `vendor/bin/sailor` → `php test.php`, verifying generated code actually works at runtime.

## Code Conventions

- Every file: `<?php declare(strict_types=1);`
- Use `protected` over `private` — allows extension for unforeseen use cases
- Full type hints on all parameters and return types
- PHPStan level max with bleedingEdge strict rules
- Namespace root: `Spawnia\Sailor`
- Test assertions use `self::assertSame()` (static calls)
- Base `TestCase` uses Mockery integration + Sailor mock traits
