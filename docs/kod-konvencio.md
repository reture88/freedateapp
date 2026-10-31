# Code Convention

This is the short reference. The detailed, task-specific rules live in
`.claude/skills/`.

## PHP

- PHP 8.4, `declare(strict_types=1)`, PSR-12.
- Type every parameter, return value, and property.
- No inline comments. DocBlocks only when the *why* is not obvious.
- Prefer `final` and `readonly` where they communicate intent.
- Nullable parameters go last, with a default of `null`.
- Backed enums only; case names UPPERCASE.
- Single-value ValueObjects must not wrap nullable attributes; pass `null`
  instead of instantiating the object.

## Architecture

- `RequestHandler/<UseCase>/`: Command/Query + Handler + Response.
- `DomainService/`: `__construct()` + `handle()` only.
- `Presentation/Controller/`: single-action `__invoke()`.
- `Presentation/Routes/routes.php`: module routes, auto-discovered.
- `Dto/ValueObject/`: typed, self-validating scalars.
- Controllers dispatch through the bus; no business logic.
- DomainServices may use Eloquent when the model is the aggregate root. Add a
  repository only when query complexity needs hiding.
- Do not nest repositories.
- No `config()` in application/domain code; inject typed config instead.
- Domain exceptions use
  `Translator::transStatic('domain.<module>.<key>')`.
- Update the migration and the seeder/factory when a model shape changes.
- Match new foreign key types to the existing schema.

## Livewire / Blade / Filament

- Livewire 3 class components; use `wire:model`, validation, loading and
  error states.
- Views live under `app/resources/views`; reusable partials under
  `app/resources/views/components` and `app/resources/views/shared`.
- CSS lives in `app/public/css/app.css`; `mtam-` prefix, CSS variables,
  mobile-first responsive rules.
- Filament 4 for admin screens; simple read lists may use Eloquent, mutations
  call services or command handlers.
- No Vue/Inertia/Node build step.

## Tests

- Feature tests for HTTP and Livewire flows; unit tests for services and
  value objects.
- Add a test for every behavior change.
- Use `RefreshDatabase` where database state matters.
- Feature test names describe the behavior, for example
  `AuthenticationFlowTest.php`.

## Commands

- `docker compose exec php composer test`
- `docker compose exec php composer phpstan`
- `docker compose exec php composer cs-check`
- `docker compose exec php composer cs-fix`
- `docker compose exec php php artisan route:list`
- `git diff --check`

## Git

- Branch: `<issue-number>-<kebab-slug>` from `main`.
- Commit: short imperative English subject, optional body, blank line, then
  `Changelog: <type>` (`added`, `changed`, `deprecated`, `removed`, `fixed`,
  `security`).
- Pull request: link the Issue with `Closes #<issue>` and use
  `.github/pull_request_template.md`.

See `.claude/skills/delivery-workflow/SKILL.md` for the full flow.
