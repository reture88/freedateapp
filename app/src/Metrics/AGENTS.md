# Metrics Domain Instructions

## Required Context

Before planning or implementing changes in this domain, read:

- `AGENTS.md` at the repo root
- `docs/architektura.md`
- `docs/freedateapp-technical-spec.md`
- `app/src/Metrics/docs/context.md`

## Working Rules

- Keep metric event names stable; the dashboard keys depend on them.
- Record metrics through `MetricRecorderService`, not ad hoc inserts.
- Keep the dashboard read-only and admin-only.
- Do not put product business logic in a metric recorder.

## Update Trigger

Update `app/src/Metrics/docs/context.md` when event names, the dashboard
payload, recording entry points, or admin access change.
