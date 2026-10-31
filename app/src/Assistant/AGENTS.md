# Assistant Domain Instructions

## Required Context

Before planning or implementing changes in this domain, read:

- `AGENTS.md` at the repo root
- `docs/architektura.md`
- `docs/freedateapp-technical-spec.md`
- `app/src/Assistant/docs/context.md`

## Working Rules

- Keep the assistant deterministic and template-based; do not introduce an
  external LLM call without a product decision.
- Post at most one assistant starter per conversation, and only while the
  conversation has no messages.
- Keep the generated copy Hungarian and clearly labeled as assistant/system
  content.
- Do not move chat transport or match eligibility into this domain.

## Update Trigger

Update `app/src/Assistant/docs/context.md` when the starter rules, waiting
period, eligibility, copy strategy, or posting entry point changes.
