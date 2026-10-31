# Harness Guide

Freedateapp uses a layered agent harness inspired by the jinn snapshot in
`../harness_definitions`. This document explains the local layout and how to
extend it.

## Layers

| Layer | Location | Purpose |
| --- | --- | --- |
| Entry point | `AGENTS.md` | Dispatcher: which skill/domain context applies to which task |
| Rules | `.claude/skills/*/SKILL.md` | Standing "how to code" rules |
| Workflows | `.claude/skills/planning`, `.claude/skills/delivery-workflow` | Step-by-step task, branch, commit, PR flows |
| Domain context | `app/src/<Module>/AGENTS.md` + `app/src/<Module>/docs/context.md` | Module boundaries, entry points, business rules, gotchas |
| Reference | `docs/architektura.md`, `docs/kod-konvencio.md` | System map and code convention |
| Tooling | `.qwen/settings.json`, `.github/` | MCP servers, permissions, issue/PR templates |

## Skill Inventory

- `laravel-cqrs-conventions` — commands, queries, handlers, domain services,
  value objects.
- `laravel-definition-of-done` — quality gates before "done".
- `livewire-blade-conventions` — Blade, Livewire, Filament, plain CSS.
- `figma-playwright-verification` — Figma MCP design reading + Playwright MCP
  visual verification.
- `delivery-workflow` — GitHub Issues, branches, commits, pull requests,
  main sync.
- `planning` — planning and task decomposition.
- `technical-writer` — Hungarian stakeholder summaries and release notes.

## Adding a Domain Context

Create two files for each module:

- `app/src/<Module>/AGENTS.md` — required context, working rules, update
  trigger.
- `app/src/<Module>/docs/context.md` — purpose, responsibilities, entry
  points, services, business rules, dependencies, data ownership, permissions,
  constraints, gotchas, related files, when to update.

Start from an existing module, copy the structure, and replace the content
with verified facts from the code. Do not copy business content from another
project.

## Using Figma and Playwright

- Figma MCP is the design source of truth.
- Playwright MCP is the visual verification tool.
- The flow is MCP-based; the repo intentionally has no `package.json`,
  Playwright config, or Node E2E suite.
- See `.claude/skills/figma-playwright-verification/SKILL.md`.

## GitHub Flow

- Every feature or bug has an Issue.
- Branch from `main`: `<issue-number>-<slug>`.
- Commit with a `Changelog:` trailer.
- Open a PR, link the Issue, attach test and visual evidence.
- Merge `main` into the branch to sync; do not force push.

## MCP and Permissions

`.qwen/settings.json` defines:

- `figma` MCP server (`https://mcp.figma.com/mcp`).
- `playwright` MCP server (`npx @playwright/mcp@latest`).
- Auto-approval for `docker compose`, `git`, and `gh` commands.

Do not commit tokens. Use environment variables or the global MCP
configuration for authentication.

## Language

- Repo artifacts are in English.
- Stakeholder summaries are in Hungarian.

## When To Update This Guide

Update when a new skill, workflow, MCP server, issue/PR template, or domain
context convention is introduced.
