# Discovery Domain Instructions

## Required Context

Before planning or implementing changes in this domain, read:

- `AGENTS.md` at the repo root
- `docs/architektura.md`
- `docs/freedateapp-technical-spec.md`
- `app/src/Discovery/docs/context.md`

## Working Rules

- Show one candidate at a time; do not add an endless feed.
- Keep eligibility filters explicit and testable.
- Record every accept/reject decision; do not silently skip a candidate.
- Only a reciprocal accept creates a matched connection and conversation.
- Keep distance and age filtering deterministic and covered by tests.

## Update Trigger

Update `app/src/Discovery/docs/context.md` when eligibility, ranking,
decision persistence, match creation, or distance behavior changes.
