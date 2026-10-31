# Repository Guidelines

## Project Snapshot

Freedateapp (MTAM) is a Laravel 12 / PHP 8.4 dating application running in
Docker. The Laravel app lives under `app/`; Docker and service wiring live in
`docker-compose.yml` and `docker/`. Framework glue is in `app/app/`, domain
modules in `app/src/<Module>/`.

Current modules: `Assistant`, `Auth`, `Chat`, `Connection`, `Discovery`,
`Legal`, `Metrics`, `Preference`, `Profile`, `Safety`, `Shared`, `User`.

UI: Blade + Livewire 3 + Filament 4 with plain CSS
(`app/public/css/app.css`). There is no Node/Vite/Vue/Inertia build step.

## Harness Layout (canonical)

- Entry point: this file.
- Rules and skills: `.claude/skills/*/SKILL.md`.
- Workflows: `.claude/skills/planning/SKILL.md`,
  `.claude/skills/delivery-workflow/SKILL.md`.
- Domain context: `app/src/<Module>/AGENTS.md` +
  `app/src/<Module>/docs/context.md`.
- References: `docs/architektura.md`, `docs/kod-konvencio.md`.
- Visual verification:
  `.claude/skills/figma-playwright-verification/SKILL.md`.
- GitHub: Issues, branches, pull requests (see Delivery Workflow).

Do not create a parallel `.windsurf/` rule tree. `.claude/skills` is the single
source of truth. If Codex or another harness needs the same content, point it
here or generate from here.

## Execution Policy

- At the start of every session, read this file and the relevant skill(s)
  before proposing or executing steps.
- If several rules apply, the more specific one wins:
  domain `AGENTS.md` > project skill > this file > generic defaults.
- Do not invent process rules when a matching skill exists.
- If a referenced file is missing, report it; do not silently substitute a
  new process.

## Interaction Policy

- Default to analysis first: when the user asks a question, answer it before
  changing files.
- For implementation requests, state the plan and affected files, then proceed
  once the user has asked for implementation (for example: "implement",
  "csináld", "kezdd el").
- Before a command with side effects outside the repo (push, pull request,
  issue creation, delete), explain it and follow the active approval mode.
- After implementation, report verification evidence: commands run, results,
  and anything not verified.

## Domain Context Policy

When a task touches a module under `app/src/<Module>/`, load:

- `docs/architektura.md`
- `app/src/<Module>/AGENTS.md` if it exists
- `app/src/<Module>/docs/context.md` if it exists

Treat `docs/context.md` as the AI-readable summary of the module. Update it
when behavior, responsibilities, service contracts, permissions, integration
boundaries, or capability handling change. A pure internal refactor does not
require an update, but a behavior change does.

If multiple domains are affected, load and maintain all of them.

## Delivery Workflow (GitHub)

Every feature or bug starts with a GitHub Issue. Use
`.claude/skills/delivery-workflow/SKILL.md` for the full flow.

- Branch from up-to-date `main`: `<issue-number>-<kebab-slug>`
  (for example `42-add-account-lifecycle`). No `feature/`, `bugfix/`,
  `hotfix/`, or `codex/` prefixes.
- Never commit code directly to `main`.
- Commit message: short imperative English subject, optional body, blank line,
  then `Changelog: <type>` where type is one of `added`, `changed`,
  `deprecated`, `removed`, `fixed`, `security`.
- Push the branch and open a pull request with
  `.github/pull_request_template.md`. Link the Issue with `Closes #<issue>`.
- If checks fail, stop and fix them before opening or updating the pull
  request. Do not bypass quality gates without an explicit user override, and
  document the failure in the pull request.
- Sync with `main` by merging `main` into the branch; do not force push.
- Delete the branch after merge.

## Quality Gates

Run inside the `php` container:

- `docker compose exec php composer test`
- `docker compose exec php composer phpstan`
- `docker compose exec php composer cs-check`
  (then `composer cs-fix` after green)
- `docker compose exec php php artisan route:list` for route changes
- `git diff --check`

For UI changes, also use the Figma + Playwright MCP flow from
`.claude/skills/figma-playwright-verification/SKILL.md`. Static checks do not
prove a visual match.

## Build, Test, and Development Commands

- `cp .env.example .env` creates local configuration.
- `docker compose up -d` starts nginx, php-fpm, MariaDB, Redis, and Reverb.
- `docker compose exec php php artisan migrate` applies migrations.
- `docker compose exec php php artisan db:seed` creates seed users.
- The app is served at `http://localhost:8080`.

## Coding Style & Naming Conventions

Use strict PHP types and PSR-12. Controllers are single-action `__invoke()`
classes. Domain services expose only `__construct()` and `handle()`.
Commands, queries, handlers, and responses use matching use-case names, for
example `LoginCommand`, `LoginCommandHandler`, and `LoginCommandResponse`.

Prefer value objects for meaningful domain scalars. Use the bus for module use
cases; controllers do not call repositories directly. See
`.claude/skills/laravel-cqrs-conventions/SKILL.md` and
`docs/kod-konvencio.md`.

## Testing Guidelines

Add or update tests with behavioral changes. Use feature tests for HTTP and
Livewire flows and unit tests for isolated services/value objects. Name tests
after the behavior under test, such as `AuthenticationFlowTest.php`.

## Language

- Repo artifacts (code, comments, docs, issues, pull requests, commits) are in
  English.
- Stakeholder-facing summaries and release notes are in Hungarian; use
  `.claude/skills/technical-writer/SKILL.md`.

## Agent-Specific Notes

- Project skills live under `.claude/skills`: Laravel/CQRS, definition of
  done, Livewire/Blade, Figma/Playwright verification, delivery workflow,
  planning, and technical writer.
- `.qwen/settings.json` enables the Figma and Playwright MCP servers and
  auto-approves `docker compose`, `git`, and `gh` commands. Do not add secrets
  there; tokens belong in the environment.
- Do not recreate unrelated external agent-framework assets.
