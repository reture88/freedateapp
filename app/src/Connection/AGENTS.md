# Connection Domain Instructions

## Required Context

Before planning or implementing changes in this domain, read:

- `AGENTS.md` at the repo root
- `docs/architektura.md`
- `docs/freedateapp-technical-spec.md`
- `app/src/Connection/docs/context.md`

## Working Rules

- Treat a connection as the single source of match state.
- Keep the profile pair ordered (`profile_one_id` < `profile_two_id`).
- Do not create conversations here; discovery creates them on a reciprocal
  match.
- Do not expose a connection to a non-participant.

## Update Trigger

Update `app/src/Connection/docs/context.md` when match eligibility, unmatch
behavior, connection status, or visibility rules change.
