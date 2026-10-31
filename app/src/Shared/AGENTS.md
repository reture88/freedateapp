# Shared Domain Instructions

## Required Context

Before planning or implementing changes in this domain, read:

- `AGENTS.md` at the repo root
- `docs/architektura.md`
- `docs/kod-konvencio.md`
- `app/src/Shared/docs/context.md`

## Working Rules

- Keep this domain cross-cutting and framework-adapter oriented.
- Do not put module business logic here.
- Keep the bus contract stable; modules depend on it.
- Do not add speculative base classes until a second real use site needs them.

## Update Trigger

Update `app/src/Shared/docs/context.md` when the bus, validation, translation,
container, or shared DTO contract changes.
