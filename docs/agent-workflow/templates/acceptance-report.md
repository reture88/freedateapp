# Acceptance Report: Title

```text
Task ID: <issue>-qa-<n>
Issue: #<issue> <url>
Branch / commit: <branch> @ <sha>
Author: qa-engineer
Date: <YYYY-MM-DD>
Verdict: pass | fail | partial
```

## Scope verified

- Feature spec: `docs/work/<issue>-<slug>/feature-spec.md`
- Technical plan: `docs/work/<issue>-<slug>/technical-plan.md`
- Diff reviewed: `<base>..<head>`

## Acceptance criteria

| # | Criterion | Result | Evidence |
| --- | --- | --- | --- |
| 1 | <criterion> | pass / fail / unverified | <command, test, flow, screenshot> |

## Quality gates

| Gate | Command | Result | Evidence |
| --- | --- | --- | --- |
| Tests | `docker compose exec php composer test` | pass / fail | <count and summary> |
| Static analysis | `docker compose exec php composer phpstan` | pass / fail | <result> |
| Code style | `docker compose exec php composer cs-check` | pass / fail | <result> |
| Routes | `docker compose exec php php artisan route:list` | pass / fail / n/a | <result> |
| Diff hygiene | `git diff --check` | pass / fail | <result> |

## Flows exercised

| Flow | Setup | Steps | Expected | Actual |
| --- | --- | --- | --- | --- |
| <flow> | <user/state> | <steps> | <expected> | <actual> |

## Regression checks

- <adjacent behavior checked and result>

## Visual evidence

- Figma frame: <name or URL>
- Desktop: <screenshot path, width>
- Mobile: <screenshot path, width>
- States: <default, empty, error, loading, success, overflow>
- Comparison: <layout, spacing, type, color, responsive, accessibility>

## Findings

### <severity>: Title

- Expected:
- Actual:
- Evidence:
- Recommended correction:

## Unverified items

- <criterion and why it could not be verified>

## Verdict

<Pass with evidence, or fail with the blocking findings.>
