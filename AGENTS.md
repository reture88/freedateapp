# Repository Guidelines

## Project Structure & Module Organization

This is a Laravel 12 / PHP 8.4 backend running in Docker. The Laravel app lives
under `app/`; Docker and service wiring live in `docker-compose.yml` and
`docker/`. Framework glue is in `app/app/`, while domain modules live in
`app/src/<Module>/`. Current modules include `Shared`, `Auth`, and `User`.

Each module should keep use cases under `RequestHandler/<UseCase>/`, business
logic in `DomainService/`, HTTP controllers in `Presentation/Controller/`, and
module routes in `Presentation/Routes/routes.php`. Tests live under
`app/tests/Feature` and `app/tests/Unit`.

## Build, Test, and Development Commands

Run PHP tooling inside the `php` container.

- `cp .env.example .env` creates local configuration.
- `docker compose up -d` starts nginx, php-fpm, MariaDB, and Redis.
- `docker compose exec php php artisan migrate` applies migrations.
- `docker compose exec php php artisan db:seed` creates seed users.
- `docker compose exec php composer test` runs PHPUnit.
- `docker compose exec php composer phpstan` runs Larastan/PHPStan.
- `docker compose exec php composer cs-check` checks PHP-CS-Fixer style.
- `docker compose exec php composer cs-fix` applies PHP-CS-Fixer changes.

The app is served at `http://localhost:8080`.

## Coding Style & Naming Conventions

Use strict PHP types and PSR-12 formatting. Controllers are single-action
`__invoke()` classes. Domain services should expose only `__construct()` and
`handle()`. Commands, queries, handlers, and responses use matching use-case
names, for example `LoginCommand`, `LoginCommandHandler`, and
`LoginCommandResponse`.

Prefer value objects for meaningful domain scalars. Avoid direct DB queries in
domain services when a repository abstraction is needed.

## Testing Guidelines

Add or update tests with behavioral changes. Use feature tests for HTTP flows
and unit tests for isolated services/value objects. Name tests after the
behavior under test, such as `AuthenticationFlowTest.php`. Before calling work
complete, run PHPUnit, PHPStan, and style checks.

## Commit & Pull Request Guidelines

The history currently uses short plain commit messages. Continue with concise
imperative messages such as `import laravel skeleton` or `add profile tests`.
Pull requests should include a summary, test evidence, linked issues when
relevant, and screenshots for UI changes.

## Agent-Specific Instructions

This repo uses Superpowers/Codex workflows. Do not import or recreate the old
external agent-framework assets. The only imported `.claude` skills are
Laravel-specific references: `laravel-definition-of-done` and
`laravel-cqrs-conventions`. Treat them as project guidance, but prefer active
Codex/Superpowers procedures when they conflict.
