# Contributing to HexMakerBundle

## Prerequisites

- PHP 8.4
- Composer 2
- Symfony CLI (optional, for local testing)

## Getting started

```bash
git clone https://github.com/takieddine-messaoudi/hex-maker-bundle
cd hex-maker-bundle
composer install
```

## Running tests

```bash
composer test          # PHPUnit test suite
composer analyze       # PHPStan static analysis (if configured)
```

## Adding a new `make:hex:*` command

1. Create a class in `src/Maker/` extending `AbstractMaker`
2. Implement `getMakerName()` (the `make:hex:<name>` command name)
3. Implement `configure()` to define input arguments
4. Implement `generate()` to render stub templates from `src/Resources/skeleton/`
5. Register the maker in `src/Resources/config/services.xml` with the `maker.command` tag
6. Add a test in `tests/Maker/` that calls the maker against a test kernel and asserts the generated files

## Code style

- PSR-12 + Symfony coding standards
- No `@var` type hints where native types suffice (PHP 8.4)
- Readonly properties by default

## Pull requests

1. Fork and create a branch (`feat/add-<name>-maker`)
2. Ensure `composer test` passes
3. Open a PR with a description of the new maker and its generated output
