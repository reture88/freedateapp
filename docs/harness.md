# Harness Guide

Freedateapp uses a Qwen Code-native agent harness. Qwen Code 0.26.4 is the
only runtime; the earlier `.claude/skills` tree was migrated to `.qwen/skills`
on 2026-11-01 and no duplicate rule tree is kept.

## Layers

| Layer | Location | Purpose |
| --- | --- | --- |
| Entry point | `AGENTS.md` | Dispatcher: project facts, policies, and which layer applies |
| Skills | `.qwen/skills/*/SKILL.md` | Standing "how to code" rules |
| Agents | `.qwen/agents/*.md` | Role definitions, tool boundaries, required context |
| Conditional rules | `.qwen/rules/*.md` | Path-gated pointers that activate backend, frontend, or harness context |
| Workflow policy | `docs/agent-workflow/` | State machine, approvals, context loading, concurrency, artifact contracts |
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
- `planning` — planning, options, task decomposition.
- `technical-writer` — Hungarian stakeholder summaries and release notes.

## Agent Inventory

| Agent | Purpose | Writes |
| --- | --- | --- |
| `orchestrator` | Selects the next valid stage, delegates, verifies, escalates | Task artifacts only |
| `product-validator` | Product scope, duplicate check, feature spec | `docs/work/**/feature-spec.md` |
| `architecture-reviewer` | Domain boundaries, data/contract impact, technical plan | `docs/work/**/technical-plan.md` |
| `backend-engineer` | Laravel CQRS implementation, tests, domain context | `app/src`, `app/tests`, backend glue |
| `frontend-engineer` | Blade, Livewire, Filament, CSS, visual evidence | views, Livewire, Filament, CSS |
| `qa-engineer` | Independent acceptance verification | `docs/work/**/acceptance-report.md` |
| `security-reviewer` | Auth, authorization, privacy, reporting/blocking review | `docs/work/**/security-review.md` |

The contract and the conditional paths are in
`docs/agent-workflow/orchestration.md` and
`docs/agent-workflow/state-machine.md`.

## Using the Agents in Qwen Code

- List or manage the definitions: `/agents manage`.
- Create a new one interactively: `/agents create`.
- Invoke a role explicitly: ask for it by name, or use the `agent` tool with
  `subagent_type` equal to the role name (`backend-engineer`,
  `qa-engineer`, and so on).
- Run the whole workflow through the orchestrator for a multi-role task; use a
  single specialist for a small, bounded task.
- Run a specialist in a worktree for parallel writes:
  `isolation: "worktree"`; review the returned branch before integrating.
- Use `run_in_background: false` when the next gate depends on the result.

Headless use:

```bash
qwen -p "Use the orchestrator to take Issue #42 through the required stages."
qwen --approval-mode plan -p "Use the architecture-reviewer on Issue #42."
```

Other Qwen Code commands used by the harness:

- `/skills` — browse the project skills; `/<skill-name>` runs one directly.
- `/permissions` — inspect the active allow/ask/deny rules.
- `qwen mcp list` — check the configured Figma and Playwright servers.
- `--approval-mode plan` — read-only review; subagents inherit the plan mode.
- `--approval-mode default` — ask before edits and commands; `auto-edit`
  approves file edits only.

## Context Loading

Every role reads a fixed context set; the matrix is in
`docs/agent-workflow/context-loading.md`. In short:

- all roles: `AGENTS.md`, the artifact contract, the approved artifacts, their
  own role definition;
- only the affected module `AGENTS.md` and `docs/context.md`;
- the skill that matches the work, not every skill.

`.qwen/rules/backend.md`, `.qwen/rules/frontend.md`, and
`.qwen/rules/harness.md` activate when a tool touches a matching path. They
point at the skill or document; they do not duplicate it.

## Approval Gates

Human approval is required for product scope, architecture, Figma direction,
Issue creation/modification, pull request creation/modification, merge, and
production deployment. Agents may draft the artifact and prepare the exact
command. See `docs/agent-workflow/approval-policy.md`.

Named subagents cannot ask the user directly. They return
`awaiting_approval`; the main Qwen session asks and records the answer.

## Artifacts

Templates live in `docs/agent-workflow/templates/`:

- `feature-spec.md`
- `technical-plan.md`
- `acceptance-report.md`
- `security-review.md`
- `design-review.md`

Durable artifacts are stored under `docs/work/<issue>-<slug>/`. Do not create
one for a trivial change; record "artifact not required" in the agent result.

## Using Figma and Playwright

- Figma MCP is the design source of truth.
- Playwright MCP is the visual verification tool.
- The flow is MCP-based; the repo intentionally has no `package.json`,
  Playwright config, or Node E2E suite.
- See `.qwen/skills/figma-playwright-verification/SKILL.md`.

## GitHub Flow

- Every feature or bug has an Issue.
- Branch from `main`: `<issue-number>-<slug>`.
- Commit with a `Changelog:` trailer.
- Open a PR, link the Issue, attach test and visual evidence.
- Merge `main` into the branch to sync; do not force push.

## MCP and Permissions

`.qwen/settings.json` defines:

- `figma` MCP server (`https://mcp.figma.com/mcp`, streamable HTTP).
- `playwright` MCP server (`npx @playwright/mcp@latest`).
- `permissions.allow`: read-only git/gh inspection, local commits, compose
  status/logs/up, the quality-gate commands, agent delegation, and the
  Figma/Playwright MCP tools.
- `permissions.ask`: push, merge, rebase/reset/stash, branch deletion,
  Issue/PR create/edit/comment/merge, compose down/restart, migrations, and
  seeding.
- `permissions.deny`: force-push patterns and reads of `.env` / `app/.env`.

Permission rules are the enforced boundary. A subagent's tool allowlist is a
second boundary. Neither replaces the approval gates.

Do not commit tokens. Use environment variables or the global MCP
configuration for authentication.

## Supported Capabilities and Limits (Qwen Code 0.26.4)

Verified in the installed documentation and build:

- Project subagents: `.qwen/agents/*.md` with `name`, `description`, `model`,
  `approvalMode`, `tools`, `disallowedTools`. An explicit `tools` list admits
  exact MCP tool names only; `mcp__server` works in `disallowedTools` and in
  permission rules, not as an allowlist declaration.
- Project skills: `.qwen/skills/*/SKILL.md`; model-invoked or `/<name>`.
- Conditional rules: `.qwen/rules/*.md` with `paths:`.
- Worktree isolation: `isolation: "worktree"` for named subagents; an
  existing worktree can be pinned with `working_dir`.
- Permission rules: `permissions.allow`, `ask`, `deny` with tool/command
  patterns.

Known limits:

- Top-level named subagents do not receive `ask_user_question`; approvals
  must return to the main session.
- Forks share the parent's working directory and have no worktree isolation;
  do not use them for parallel writes.
- A subagent's write scope is an instruction, not a filesystem sandbox. The
  frontend and QA roles inherit the tool set so the Figma and Playwright MCP
  tools are available; their boundaries are the task envelope, the QA
  blocklist on `edit`, and the review evidence.
- Nested agents run in the foreground; a nested background launch is rejected.
- Worktree changes must be reviewed and integrated manually; Qwen Code does
  not merge them.
- There is no automatic stage engine. The state machine is enforced by the
  orchestrator prompt, the artifacts, and the human gates, not by code.

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

## When To Update This Guide

Update when a skill, agent role, rule, workflow document, MCP server,
issue/PR template, or domain context convention is introduced or changed, and
when a Qwen Code upgrade changes a documented capability or limit.
