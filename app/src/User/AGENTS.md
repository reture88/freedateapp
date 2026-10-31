# User Domain Instructions

## Required Context

Before planning or implementing changes in this domain, read:

- `AGENTS.md` at the repo root
- `docs/architektura.md`
- `docs/freedateapp-technical-spec.md`
- `app/src/User/docs/context.md`

## Working Rules

- Treat `AccountStatus` as the single source of truth for account access.
- Keep `is_active` in sync with `account_status`; it is a legacy mirror.
- Account moderation writes an audit log.
- Deactivate/delete hides the dating profile.
- Do not put dating-profile content in this domain; use `Profile`.

## Update Trigger

Update `app/src/User/docs/context.md` when account statuses, moderation
actions, profile settings, addresses, password rules, or account lifecycle
behavior change.
