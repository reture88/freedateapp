---
name: laravel-definition-of-done
description: 'Checklist to run before declaring PHP/Laravel backend work in this repo complete. Use before committing backend changes, when finishing a task in app/, or when the user asks "is this done", "ready to commit", or "definition of done" for this Laravel app.'
---

# Laravel Definition of Done

Adapted from the reference implementation's `.windsurf/workflows/dod.md`. Everything below runs inside the `php` container (`docker compose exec php ...`) — there is no local PHP.

## Run every time before calling backend work done

1. **Static analysis**
   ```bash
   docker compose exec php composer phpstan
   ```
   Runs `phpstan`/`larastan` at level 6 (`app/phpstan.neon`). Must report `[OK] No errors`. Two pre-existing findings are deliberately suppressed via `ignoreErrors` in that config (documented inline there) — don't silence a *new* finding the same way without equally solid justification; fix it instead.

2. **Code style**
   ```bash
   docker compose exec php composer cs-check   # dry-run, shows diff
   docker compose exec php composer cs-fix     # applies fixes
   ```
   PSR-12 via `app/.php-cs-fixer.dist.php`. Run `cs-fix` and re-stage before committing if `cs-check` reports anything.

3. **Tests**
   ```bash
   docker compose exec php composer test
   ```
   Runs `vendor/bin/phpunit -c phpunit.xml` (`tests/Unit`, `tests/Feature`, sqlite in-memory DB, array session/cache/queue — no dependency on the dev MariaDB/Redis containers). If the change adds a new Command/Query/route, add a test for it under `tests/Feature/<Module>/` or `tests/Unit/<Module>/`, following `tests/Feature/Auth/AuthenticationFlowTest.php` as the shape to copy.

4. **Route sanity**
   If the change touches routing or adds a new Command/Query:
   ```bash
   docker compose exec php php artisan route:list
   ```
   confirm the expected route(s) appear with no errors.

5. **End-to-end smoke check**
   Static analysis and tests don't prove a web flow works against the real stack. For anything touching a Controller, route, or view, drive it for real (curl or browser) against `http://localhost:8080`: start the stack with `docker compose up -d`, exercise the touched flow, and inspect the response. Check `docker compose exec php cat storage/logs/laravel.log` for anything unexpected afterward (absence of the file means nothing was logged).

6. **Visual verification for UI changes**
   If the change touches Blade, Livewire, Filament, or CSS, use the Figma + Playwright MCP flow in `figma-playwright-verification`: read the relevant Figma frame, screenshot the running screen at desktop and mobile widths, compare, and record the evidence. Static checks and PHPUnit do not prove a visual match.

7. **Diff hygiene**
   ```bash
   git diff --check
   ```
   Must report no whitespace errors.

8. **Delivery gate**
   Before committing, check `delivery-workflow`: the change belongs to a GitHub Issue, the branch is `<issue>-<slug>`, and the commit has a `Changelog:` trailer.

9. **Structural conventions**
   Cross-check the change against `laravel-cqrs-conventions` (this repo's skill) — thin Controller/Handler, logic in DomainService, no bare scalars for identifiers, no DB calls in Services.

## If you add a new composer dev dependency

Run `docker compose exec php composer update <package> --with-all-dependencies` rather than a full `composer update`, so unrelated dependencies don't drift.
