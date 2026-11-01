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

Qwen Code is the only agent runtime. The harness is Qwen-native:

- Entry point: this file.
- Runtime: Qwen Code 0.26.4.
- Harness guide: `docs/harness.md`.
- Skills: `.qwen/skills/*/SKILL.md`.
- Subagents: `.qwen/agents/*.md`.
- Conditional context rules: `.qwen/rules/*.md`.
- Workflow policy: `docs/agent-workflow/` (state machine, approval, context
  loading, concurrency, artifact contracts, templates).
- Domain context: `app/src/<Module>/AGENTS.md` +
  `app/src/<Module>/docs/context.md`.
- References: `docs/architektura.md`, `docs/kod-konvencio.md`.
- Visual verification: `.qwen/skills/figma-playwright-verification/SKILL.md`.
- GitHub: Issues, branches, pull requests (see Delivery Workflow).
- MCP and permissions: `.qwen/settings.json`.

`.qwen/skills` is the single source of truth for standing rules. Do not
duplicate the same rule in `.claude/`, `.codex/`, `.windsurf/`, or another
harness tree. If another tool needs the content, point it here. Claude Code and
Codex are not part of the runtime; historical references to them may remain
only where they explain the origin of a rule.

Harness ownership is explicit: the main session writes `.qwen/settings.json`,
`.qwen/agents/**`, `.qwen/skills/**`, and `.qwen/rules/**`; the orchestrator
writes `AGENTS.md`, `docs/harness.md`, and `docs/agent-workflow/**` only for a
`docs-harness` task. Reviewers are read-only. See
`docs/agent-workflow/concurrency-policy.md`.

If a referenced file is missing, report it; do not silently substitute a new
process.

## Execution Policy

- At the start of every session, read this file and the relevant skill(s)
  before proposing or executing steps.
- If several rules apply, the more specific one wins:
  domain `AGENTS.md` > project skill > this file > generic defaults.
- `.qwen/rules/*.md` activate automatically when a tool touches a matching
  path. They are pointers to the authoritative skill or document, not a
  duplicate of it.
- Do not invent process rules when a matching skill exists.
- Follow `docs/agent-workflow/state-machine.md` only for work that uses the
  agent roles. A direct, small change still follows the delivery workflow.

## Agent Roles and Orchestration

The harness separates implementation from verification and approval. The roles
are defined in `.qwen/agents/`:

| Role | Delivers | Must not do |
| --- | --- | --- |
| `orchestrator` | Stage selection, delegation, evidence evaluation, task artifacts, `docs-harness` docs | Edit application code, `.qwen/**` configuration, or approve a gate |
| `product-validator` | Feature spec content (read-only) | Approve its own proposal or choose architecture |
| `architecture-reviewer` | Technical plan content (read-only) | Change approved scope or implement |
| `backend-engineer` | Laravel backend code, tests, domain context | Edit UI outside its scope or approve its own work |
| `frontend-engineer` | Blade/Livewire/Filament/CSS, screenshots | Invent a design system or approve its own work |
| `qa-engineer` | Acceptance report content (read-only on code) | Implement the feature it reviews or trust claims |
| `security-reviewer` | Security review content (read-only) | Implement fixes or review its own work |

The main Qwen session is the human-facing entry point and the only agent that
can ask the user. It is also the only commit owner. Named subagents return
`awaiting_approval` or a commit proposal; the main session records the human
decision and runs `git add`/`git commit`/`git push`.

Read `docs/agent-workflow/orchestration.md` for the task envelope and the
delegation contract. Read `docs/agent-workflow/approval-policy.md` before any
action that leaves the repository.

## Interaction Policy

- Default to analysis first: when the user asks a question, answer it before
  changing files.
- For implementation requests, state the plan and affected files, then proceed
  once the user has asked for implementation (for example: "implement",
  "csináld", "kezdd el").
- Before a command with side effects outside the repo (push, pull request,
  issue creation, delete), explain it and follow the active approval mode.
- Creating or editing an Issue, opening or editing a pull request, merging,
  and deleting a branch with unmerged work require explicit human approval
  after the draft exists.
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

If multiple domains are affected, load and maintain all of them. Do not load
unrelated domain contexts; see `docs/agent-workflow/context-loading.md`.

## Delivery Workflow (GitHub)

Every feature or bug starts with a GitHub Issue. Use
`.qwen/skills/delivery-workflow/SKILL.md` for the full flow.

- Record a `Task type:` in the Issue body: `backend-bugfix`,
  `backend-feature`, `frontend-feature`, `fullstack-feature`, `security-fix`,
  or `docs-harness`. The orchestrator returns `blocked` when it is missing.
- Branch from up-to-date `main`: `<issue-number>-<kebab-slug>`
  (for example `42-add-account-lifecycle`). No `feature/`, `bugfix/`,
  `hotfix/`, or `codex/` prefixes.
- Never commit code directly to `main`. The main session is the only commit
  owner; subagents return a commit proposal.
- Implementation starts only after `Awaiting architecture approval` is
  recorded, including for light and harness plans.
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
  (then `composer cs-fix` if it reports a diff)
- `docker compose exec php php artisan route:list` for route changes
- `git diff --check`

For UI changes, also use the Figma + Playwright MCP flow from
`.qwen/skills/figma-playwright-verification/SKILL.md`. Static checks do not
prove a visual match.

For work that uses the agent roles, QA verifies the evidence independently and
security review is mandatory for auth, profile visibility, location, reporting,
blocking, moderation, sensitive-data changes, and `.qwen/settings.json`, MCP
servers, or agent tool permissions. QA rejects an implementation result whose
`Context status` is not `complete` or whose `Required context` /
`Files inspected` / `Missing context` fields are absent.

Changing `.qwen/settings.json`, agent definitions, skills, or rules also
requires the harness validation: JSON parse, referenced-path check, static
harness validator, and `git diff --check`.

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
`.qwen/skills/laravel-cqrs-conventions/SKILL.md` and
`docs/kod-konvencio.md`.

## Testing Guidelines

Add or update tests with behavioral changes. Use feature tests for HTTP and
Livewire flows and unit tests for isolated services/value objects. Name tests
after the behavior under test, such as `AuthenticationFlowTest.php`.

An implementation agent does not approve its own tests. QA inspects the tests,
the gate evidence, and maps each acceptance criterion to evidence. The main
session runs the gates; QA has no shell.

## Language

- Repo artifacts (code, comments, docs, issues, pull requests, commits) are in
  English.
- Stakeholder-facing summaries and release notes are in Hungarian; the main
  session invokes `.qwen/skills/technical-writer/SKILL.md` for them. No
  subagent owns that skill.

## Agent-Specific Notes

- Qwen Code discovers project skills in `.qwen/skills`, project subagents in
  `.qwen/agents`, and conditional rules in `.qwen/rules`. It does not read
  `.claude/skills`.
- `.qwen/settings.json` keeps the Figma and Playwright MCP servers. It
  auto-approves read-only inspection and the quality gates; `git add`,
  `git commit`, branch switching, push, Issue/PR changes, merge, migrations,
  `compose down`, and edits under `.qwen/**` require approval; force pushes are
  denied. Do not add secrets there; tokens belong in the environment.
- The main session is the only commit owner. Subagents never commit, push,
  switch branches, or merge.
- Do not recreate unrelated external agent-framework assets.
