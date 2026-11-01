# Context Loading Policy

Every role loads a deterministic, minimal context set. Agents do not decide
from scratch which skills and domains matter.

## Universal context

Every agent reads, in order:

1. `AGENTS.md`
2. `docs/agent-workflow/artifact-contracts.md`
3. The approved artifacts named in the task envelope
4. Its own role definition in `.qwen/agents/`

The orchestrator additionally reads every file in `docs/agent-workflow/`, plus
`.qwen/skills/planning` and `.qwen/skills/delivery-workflow`.

## Role matrix

| Role | Required | Conditional | Do not load |
| --- | --- | --- | --- |
| orchestrator | `docs/harness.md`, all `docs/agent-workflow/*.md`, planning, delivery-workflow | The Issue and approved artifacts | Every domain context up front |
| product-validator | `docs/freedateapp-product-scope.md`, `docs/freedateapp-technical-spec.md`, `docs/architektura.md`, feature-spec template, planning | The affected module `AGENTS.md` / `docs/context.md` | Implementation-level CQRS conventions |
| architecture-reviewer | `docs/architektura.md`, `docs/kod-konvencio.md`, `.qwen/skills/laravel-cqrs-conventions`, planning, technical-plan template | The affected module contexts; the feature spec; migrations and routes actually touched | Unaffected modules; frontend design detail |
| backend-engineer | `docs/architektura.md`, `docs/kod-konvencio.md`, `.qwen/skills/laravel-cqrs-conventions`, `.qwen/skills/laravel-definition-of-done`, approval and concurrency policies | The feature spec, technical plan, affected module contexts, affected tests and migrations | Frontend skill and design brief unless the task is UI-coupled |
| frontend-engineer | `docs/architektura.md`, `docs/kod-konvencio.md`, `.qwen/skills/livewire-blade-conventions`, `.qwen/skills/figma-playwright-verification`, `docs/freedateapp-figma-design-brief.md` | The feature spec, technical plan, Figma frame, affected module contexts, design system classes in use | Backend implementation detail beyond the commands and queries the UI calls |
| qa-engineer | `docs/architektura.md`, `.qwen/skills/laravel-definition-of-done`, acceptance-report template, the feature spec and plan | `.qwen/skills/figma-playwright-verification` for UI changes; affected module contexts; the implementation result | Every module's full context; unrelated Git history |
| security-reviewer | `docs/freedateapp-technical-spec.md` (Security and Privacy Requirements), `docs/freedateapp-product-scope.md` (privacy, safety, assistant), `docs/architektura.md` (access gates), `.qwen/skills/laravel-cqrs-conventions`, security-review template | The diff, affected routes/middleware/policies/services, affected module contexts, relevant tests | Unaffected modules; visual design detail |

## Domain selection

Load a module context only when the task envelope names that module or the
technical plan assigns work there. The module's required reading is:

- `app/src/<Module>/AGENTS.md`
- `app/src/<Module>/docs/context.md`

If two modules are affected, load both and state the integration boundary. Do
not load a third "for background". Use `grep_search` and `glob` inside the
module instead of reading the whole tree.

## Activation rules

- `.qwen/rules/backend.md` activates when a tool touches `app/src`,
  `app/tests`, or `app/database`.
- `.qwen/rules/frontend.md` activates when a tool touches
  `app/resources/views`, `app/app/Livewire`, `app/app/Filament`, or
  `app/public/css`.
- `.qwen/rules/harness.md` activates when a tool touches `.qwen/` or
  `docs/agent-workflow/`.

Rules are pointers to the authoritative skill or document; they do not repeat
the rule content. A rule firing is a reminder to read, not a substitute for
reading.

## Missing or stale context

- A missing required file stops the agent with `blocked`, naming the path.
- A required artifact with no approval block is a draft, not approved context.
- If an approved artifact changed after approval, re-read it and check whether
  the approval still covers the new version. If not, return
  `awaiting_approval`.
- Do not use chat history, memory, or a previous agent result as the only
  source for current code behavior. Read the code.

## Context budget

- Prefer exact paths over directory reads.
- Read the affected test and route files, not the whole test suite.
- Load one module context per affected module, not all twelve.
- Do not reload a file already in context unless it changed.
- A specialist may read outside its role matrix only to answer one specific
  question, and must name the file in `Files inspected`.
