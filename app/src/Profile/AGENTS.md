# Profile Domain Instructions

## Required Context

Before planning or implementing changes in this domain, read:

- `AGENTS.md` at the repo root
- `docs/architektura.md`
- `docs/freedateapp-technical-spec.md`
- `app/src/Profile/docs/context.md`

## Working Rules

- Keep one dating profile per user.
- Keep profile visibility and moderation status separate from account status.
- Enforce the 18+ rule at onboarding and when a profile is shown.
- Use the existing photo limit and primary-photo behavior.
- Do not let a non-participant view another user's profile.

## Update Trigger

Update `app/src/Profile/docs/context.md` when onboarding fields, visibility,
photo rules, profile access, or the 18+ rule change.
