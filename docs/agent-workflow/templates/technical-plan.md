# Technical Plan: Title

```text
Task ID: <issue>-architecture-<n>
Issue: #<issue> <url>
Author: architecture-reviewer
Date: <YYYY-MM-DD>
Status: pending | in_progress | blocked | awaiting_approval | completed | failed | rejected
Feature spec: docs/work/<issue>-<slug>/feature-spec.md
Light plan: yes | no
```

## Current state

<Verified behavior from code, routes, services, and tests. Cite paths.>

## Affected domains

| Module | Responsibility in this change | Stays out of scope |
| --- | --- | --- |
| `<Module>` | <responsibility> | <boundary> |

## Options considered

### Option A — <name>

- Approach:
- Pros:
- Cons:
- Risk:

### Option B — <name>

- Approach:
- Pros:
- Cons:
- Risk:

### Decision

<Selected option and why the others lose.>

## CQRS request flow

1. Route: `<path>`
2. Controller: `<class>`
3. Command/Query: `<class>`
4. Handler: `<class>`
5. Domain service: `<class>::handle()`
6. Persistence / response: <detail>

## Data and schema

- Tables/columns/indexes:
- Foreign-key types:
- Migration:
- Seeder/factory:
- Backward compatibility:

## Contracts

- Routes and middleware order:
- Command/query names:
- Response shape:
- Events and listeners:
- Livewire components / Filament resources:
- Reverb channels:

## Risks and mitigations

| Risk | Impact | Mitigation | Verification |
| --- | --- | --- | --- |
| <risk> | <impact> | <mitigation> | <check> |

## Implementation phases

| Phase | Owner | Write scope | Depends on |
| --- | --- | --- | --- |
| 1. Backend | backend-engineer | <globs> | <dependency> |
| 2. Frontend | frontend-engineer | <globs> | <dependency> |

## Domain context updates

- `app/src/<Module>/docs/context.md`: <what changes>

## Verification plan

Commands for the implementation and QA; this reviewer is read-only.

- `docker compose exec php composer test`
- `docker compose exec php composer phpstan`
- `docker compose exec php composer cs-check`
- `docker compose exec php php artisan route:list` (if routes changed)
- `git diff --check`
- Visual: <Figma frame and Playwright screenshots, or "not applicable">

## Context attestation

```text
Required context:
Files inspected:
Missing context:
Context status: complete | partial | blocked
```

## Open questions

- <question and recommendation>

## Approval

```text
Approved by:
Date:
Scope:
Conditions:
Evidence:
```
