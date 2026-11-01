---
name: planning
description: 'Use when a task is large, ambiguous, or spans backend, frontend and QA: write a plan before implementation, compare at least two architecture options, define domain boundaries and migration phases, and split work into BE/FE/QA subtasks.'
---

# Planning Workflow

Use this workflow for epics, multi-module changes, new domains, and any task
where the implementation path is not obvious. Keep planning separate from
implementation.

## 1. Validate the current state

- Read `AGENTS.md`, `docs/architektura.md`, and the affected domain
  `AGENTS.md` + `docs/context.md`.
- Inspect the current routes, services, models, and tests.
- Confirm the scope boundary and backward-compatibility risks.
- Identify what is explicitly out of scope.

## 2. Write the plan

Prefer `docs/work/<issue>-<slug>/technical-plan.md` (the canonical planning
artifact; see `docs/agent-workflow/artifact-contracts.md`). Historical plans
live under `docs/superpowers/plans/`.

The plan must include:

- current-state summary,
- at least two architecture options,
- the selected option and the reasoning,
- domain boundaries (DDD/CQRS),
- migration phases,
- data and API changes,
- risks and mitigations,
- acceptance criteria.

## 3. Split the work

Create or update the GitHub Issue with ordered subtasks:

- `[BE]` — backend, domain, migrations, tests.
- `[FE]` — Blade/Livewire/Filament/CSS.
- `[QA]` — behavior, regression, visual verification.
- `[FEBE]` — a combined backend + frontend slice when splitting is artificial.

Link the subtasks to the main Issue. Do not merge unrelated work into the same
planning batch.

## 4. Approval gate

- Ask for approval before creating or updating GitHub Issues.
- Do not start implementation until the architecture plan is approved in the
  `Awaiting architecture approval` stage, including for a light or harness
  plan.
- If the plan changes during implementation, update the plan file or Issue
  rather than silently diverging.
- The main session is the only commit owner; planners return the plan text and
  implementation agents return their changes.

## 5. Output

Return:

- the main Issue number,
- the plan file path,
- the ordered BE/FE/QA subtask numbers,
- a short note about the selected architecture option.

## Related

- `delivery-workflow` — Issue/branch/commit/PR flow.
- `docs/architektura.md` — system map.
- `app/src/<Module>/docs/context.md` — domain facts.
