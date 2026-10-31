# Preference Domain Instructions

## Required Context

Before planning or implementing changes in this domain, read:

- `AGENTS.md` at the repo root
- `docs/architektura.md`
- `docs/freedateapp-technical-spec.md`
- `app/src/Preference/docs/context.md`

## Working Rules

- Keep preference defaults explicit and centralized in
  `EnsureDefaultPreferenceService`.
- Keep preferences owned by the dating profile, not the user account.
- Do not duplicate preference filtering in controllers or views; discovery
  consumes the stored preference.
- Keep the summary human-readable and consistent with the stored values.

## Update Trigger

Update `app/src/Preference/docs/context.md` when a preference field, default,
validation rule, or discovery contract changes.
