# Auth Domain Instructions

## Required Context

Before planning or implementing changes in this domain, read:

- `AGENTS.md` at the repo root
- `docs/architektura.md`
- `docs/freedateapp-technical-spec.md`
- `app/src/Auth/docs/context.md`

## Working Rules

- Keep authentication, session entry, email verification, and password reset
  inside this domain.
- Use `AuthDestinationService` as the single source of truth for post-login
  and post-verification destinations.
- Keep the account-status gate (`account.active`) before the verification gate
  (`verified`) so restricted accounts see the status page.
- Do not move account data ownership out of `User`.

## Update Trigger

Update `app/src/Auth/docs/context.md` when login, registration, verification,
password reset, destination rules, rate limits, or route middleware change.
