# Technical Plan: Qwen harness remediation (F-01–F-07)

```text
Task ID: 3-architecture-1
Issue: #3 https://github.com/reture88/freedateapp/issues/3
Author: architecture-reviewer
Writer: main session
Date: 2026-11-01
Status: completed
Feature spec: not required (docs-harness path)
Light plan: no
```

## Current state

The 2026-11-01 audit found seven major gaps: context loading was attestation-only, reviewer write boundaries were prompt-only, commit ownership was contradictory and auto-approved, `docs-harness` had no writer, the planning skill conflicted with the artifact contract, task types/architecture approval were under-specified, and the orchestrator did not emit its envelope.

## Affected domains

| Module | Responsibility in this change | Stays out of scope |
| --- | --- | --- |
| Harness (`.qwen/**`, `docs/agent-workflow/**`, `AGENTS.md`, `docs/harness.md`) | Context attestation, tool boundaries, ownership, workflow policy | Application code, schema, migrations, Composer dependencies |
| Qwen configuration | Permissions, agent definitions, skills, rules | Figma configuration and authentication |

## Options considered

### Option A — documentation-only remediation

- Approach: update the prose and templates, leave tools and permissions unchanged.
- Pros: minimal diff.
- Cons: reviewer writes, auto-approved commits, and the missing ownership remain unenforced.

### Option B — tool and permission boundaries plus workflow contracts

- Approach: remove reviewer write/shell tools, add context attestation fields, make the main session the commit owner, add harness ownership and task-type gates, and tighten permissions.
- Pros: enforceable where Qwen supports it; honest about prompt-only limits.
- Cons: QA loses shell and must inspect gate evidence; the main session must run gates and commits.

### Decision

Option B. Qwen Code 0.26.4 supports per-agent `tools`/`disallowedTools` and global `permissions.ask`/`deny`; it does not provide per-agent paths or a stage engine. The plan uses the supported boundaries and documents the rest.

## Contracts

- Agent result envelope: 14 fields, including `Required context`, `Files inspected`, `Missing context`, `Context status`.
- Reviewer tools: product/architecture/security have `read_file`, `grep_search`, `glob`; QA additionally blocks shell, file writes, agent messaging, tool bridging, and file-writing Playwright tools.
- Commit owner: main session only; `git add`/`commit`/`checkout`/`switch`/`restore`/`cherry-pick`/`worktree` and global-flag forms are `ask`.
- Harness writer: main session for `.qwen/**`; orchestrator for `AGENTS.md`, `docs/harness.md`, `docs/agent-workflow/**` on `docs-harness`.
- Task types: six canonical values in both Issue templates and the orchestration envelope.
- Planning artifact: `docs/work/<issue>-<slug>/technical-plan.md`, at least two options.
- Architecture gate: every implementation path includes `Awaiting architecture approval`.

## Risks and mitigations

| Risk | Impact | Mitigation |
| --- | --- | --- |
| Context attestation is not runtime-verified | A false claim survives | QA compares required/inspected lists; limitation documented |
| `ask` is prompt-only | A yolo run bypasses commit/branch prompts | Documented; main-session ownership plus approval policy |
| QA has no shell | Gate evidence comes from the main session | QA inspects evidence and names missing commands |
| Playwright needs Chrome | Visual calls fail | Frontend captures screenshots; main session installs/verifies Chrome |

## Affected files

- Qwen configuration: `.qwen/settings.json`, `.qwen/agents/*.md`,
  `.qwen/rules/backend.md`, `.qwen/rules/harness.md`, `.qwen/skills/*/SKILL.md`.
- Workflow policy: `AGENTS.md`, `docs/harness.md`,
  `docs/agent-workflow/*.md`, `docs/agent-workflow/templates/*.md`,
  `docs/agent-workflow/validate-harness.py`.
- GitHub templates: `.github/ISSUE_TEMPLATE/feature.md`,
  `.github/ISSUE_TEMPLATE/bug.md`, `.github/pull_request_template.md`.
- Repository hygiene: `.gitignore`.
- No `app/**`, database, migration, or Composer dependency change.

## Rollback

Revert the remediation commit on `3-qwen-native-agent-harness` with
`git revert <commit>`; no database or migration rollback is needed. Re-run
`python3 docs/agent-workflow/validate-harness.py` and Qwen discovery after the
revert.

## Implementation phases

| Phase | Owner | Write scope | Depends on |
| --- | --- | --- | --- |
| 1. Policy and contracts | orchestrator | `AGENTS.md`, `docs/harness.md`, `docs/agent-workflow/**` | audit findings |
| 2. Qwen configuration | main session | `.qwen/**` | policy |
| 3. QA | qa-engineer | read-only | implementation result |
| 4. Security review | security-reviewer | read-only; returns review text | configuration + policy |

## Verification plan

Commands for the main session:

- `python3 docs/agent-workflow/validate-harness.py`
- `python3 -m json.tool .qwen/settings.json`
- `git diff --check`
- Qwen discovery: 7 agents, 7 skills
- Live orchestrator envelope: 14 fields
- No `app/**` diff

## Context attestation

```text
Required context: AGENTS.md; docs/harness.md; docs/architektura.md; docs/kod-konvencio.md; docs/agent-workflow/README.md; docs/agent-workflow/artifact-contracts.md; docs/agent-workflow/context-loading.md; docs/agent-workflow/orchestration.md; docs/agent-workflow/state-machine.md; docs/agent-workflow/approval-policy.md; docs/agent-workflow/concurrency-policy.md; docs/agent-workflow/templates/technical-plan.md; docs/agent-workflow/templates/acceptance-report.md; .qwen/agents/orchestrator.md; .qwen/agents/product-validator.md; .qwen/agents/architecture-reviewer.md; .qwen/agents/backend-engineer.md; .qwen/agents/frontend-engineer.md; .qwen/agents/qa-engineer.md; .qwen/agents/security-reviewer.md; .qwen/skills/laravel-cqrs-conventions/SKILL.md; .qwen/skills/planning/SKILL.md; .qwen/skills/delivery-workflow/SKILL.md; .qwen/skills/laravel-definition-of-done/SKILL.md; .qwen/settings.json; .qwen/rules/backend.md; .qwen/rules/harness.md
Files inspected: AGENTS.md; docs/harness.md; docs/architektura.md; docs/kod-konvencio.md; docs/agent-workflow/README.md; docs/agent-workflow/artifact-contracts.md; docs/agent-workflow/context-loading.md; docs/agent-workflow/orchestration.md; docs/agent-workflow/state-machine.md; docs/agent-workflow/approval-policy.md; docs/agent-workflow/concurrency-policy.md; docs/agent-workflow/templates/technical-plan.md; docs/agent-workflow/templates/acceptance-report.md; .qwen/agents/orchestrator.md; .qwen/agents/product-validator.md; .qwen/agents/architecture-reviewer.md; .qwen/agents/backend-engineer.md; .qwen/agents/frontend-engineer.md; .qwen/agents/qa-engineer.md; .qwen/agents/security-reviewer.md; .qwen/skills/laravel-cqrs-conventions/SKILL.md; .qwen/skills/planning/SKILL.md; .qwen/skills/delivery-workflow/SKILL.md; .qwen/skills/laravel-definition-of-done/SKILL.md; .qwen/settings.json; .qwen/rules/backend.md; .qwen/rules/harness.md
Missing context: none
Context status: complete
```

## Approval

```text
Approved by: reture
Date: 2026-11-01
Scope: F-01–F-07 harness remediation, branch 3-qwen-native-agent-harness, base commit 946e861 + working tree
Conditions: update PR #4 only; do not merge
Evidence: https://github.com/reture88/freedateapp/issues/3 ; docs/work/3-qwen-native-agent-harness/technical-plan.md ; docs/work/3-qwen-native-agent-harness/security-review.md
```
