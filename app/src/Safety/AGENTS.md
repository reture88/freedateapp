# Safety Domain Instructions

## Required Context

Before planning or implementing changes in this domain, read:

- `AGENTS.md` at the repo root
- `docs/architektura.md`
- `docs/freedateapp-technical-spec.md`
- `app/src/Safety/docs/context.md`

## Working Rules

- Keep reports, blocks, and moderation audit inside this domain.
- Never expose report details to the reported user.
- A block must remove the pair from discovery and mark the connection blocked.
- Admin moderation must write an audit log entry.
- Do not change account status here; account moderation belongs to `User`.

## Update Trigger

Update `app/src/Safety/docs/context.md` when report/block behavior, admin
actions, audit logging, or visibility effects change.
