---
description: Backend and domain context loading for PHP changes in Freedateapp
paths:
  - 'app/src/**'
  - 'app/tests/**'
  - 'app/database/**'
  - 'app/routes/**'
  - 'app/config/**'
  - 'app/app/Http/**'
  - 'app/app/Models/**'
  - 'app/app/Providers/**'
---

Before editing PHP under these paths:

- Read `.qwen/skills/laravel-cqrs-conventions/SKILL.md` and
  `.qwen/skills/laravel-definition-of-done/SKILL.md` if they are not already
  in context.
- Identify the affected module and read only
  `app/src/<Module>/AGENTS.md` and `app/src/<Module>/docs/context.md`.
- Treat `docs/architektura.md` and `docs/kod-konvencio.md` as the architecture
  and convention baseline.
- Update the module `docs/context.md` when behavior, responsibilities, service
  contracts, permissions, integration boundaries, or capability handling
  change.
- Run the gates in the `php` container; do not claim done without output.

This rule is a pointer, not a duplicate of the skills.
