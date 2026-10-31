# Legal Domain Instructions

## Required Context

Before planning or implementing changes in this domain, read:

- `AGENTS.md` at the repo root
- `docs/architektura.md`
- `app/src/Legal/docs/context.md`

## Working Rules

- Keep the legal pages public and server-rendered.
- Do not invent legal wording; mark copy that still needs legal review.
- Keep the routes stable (`legal.terms`, `legal.privacy`); other pages link
  to them.

## Update Trigger

Update `app/src/Legal/docs/context.md` when the pages, routes, or legal-copy
status change.
