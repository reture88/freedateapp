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
| `orchestrator` | Selects the next valid stage, delegates, verifies, escalates | Task artifacts; `AGENTS.md`, `docs/harness.md`, `docs/agent-workflow/**` for `docs-harness` |
| `product-validator` | Product scope, duplicate check, feature spec | Read-only; returns spec text |
| `architecture-reviewer` | Domain boundaries, data/contract impact, technical plan | Read-only; returns plan text |
| `backend-engineer` | Laravel CQRS implementation, tests, domain context | `app/src`, `app/tests`, backend glue |
| `frontend-engineer` | Blade, Livewire, Filament, CSS, visual evidence | views, Livewire, Filament, CSS |
| `qa-engineer` | Independent acceptance verification | Read-only; returns report text |
| `security-reviewer` | Auth, authorization, privacy, reporting/blocking review | Read-only; returns review text |

The **main session** is the only commit owner and the only writer of
`.qwen/settings.json`, `.qwen/agents/**`, `.qwen/skills/**`, and
`.qwen/rules/**`. `Edit(./.qwen/**)` is an `ask` permission. There is no
unrestricted harness-writer role; see
`docs/agent-workflow/concurrency-policy.md`.

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

Every result carries `Required context`, `Files inspected`, `Missing context`,
and `Context status`. A role may return `completed` only with
`Context status: complete`; QA and the orchestrator reject a result missing the
attestation. This is a reviewable contract, not a filesystem sandbox.

`.qwen/rules/backend.md`, `.qwen/rules/frontend.md`, and
`.qwen/rules/harness.md` activate when a tool touches a matching path. They
point at the skill or document; they do not duplicate it.

## Approval Gates

Human approval is required for product scope, architecture (including light and
harness plans), Figma direction, Issue creation/modification, commit, pull
request creation/modification, merge, and production deployment. Agents may
draft the artifact and prepare the exact command. See
`docs/agent-workflow/approval-policy.md`.

The main session is the only commit owner. Named subagents cannot ask the user
directly; they return `awaiting_approval` or a commit proposal, and the main
session asks and records the answer.

## Artifacts

Templates live in `docs/agent-workflow/templates/`:

- `feature-spec.md`
- `technical-plan.md`
- `acceptance-report.md`
- `security-review.md`
- `design-review.md`

Durable artifacts are stored under `docs/work/<issue>-<slug>/`. The
orchestrator writes them; the author returns the full text in its result. Do
not create one for a trivial change; record "artifact not required" in the
agent result.

## Using Figma and Playwright

- Figma MCP is the design source of truth, but the Figma subscription is
  currently expired; agents must report the blocker instead of inventing a
  frame.
- Playwright MCP is the visual verification tool. The server exposes the
  browser tools; install Chrome (`npx playwright install chrome`) before using
  them.
- The flow is MCP-based; the repo intentionally has no `package.json`,
  Playwright config, or Node E2E suite.
- See `.qwen/skills/figma-playwright-verification/SKILL.md`.

## GitHub Flow

- Every feature or bug has an Issue with a `Task type:` field.
- Branch from `main`: `<issue-number>-<slug>`.
- Record `Awaiting architecture approval` before implementation.
- The main session commits with a `Changelog:` trailer; subagents return a
  commit proposal.
- Open a PR, link the Issue, attach test and visual evidence.
- Merge `main` into the branch to sync; do not force push.

## MCP and Permissions

`.qwen/settings.json` defines:

- `figma` MCP server (`https://mcp.figma.com/mcp`, streamable HTTP).
- `playwright` MCP server (`npx @playwright/mcp@0.0.84`).
- `permissions.allow`: read-only git/gh inspection, compose status/logs/up,
  the quality-gate commands (`composer test`, `phpstan`, `cs-check`,
  `route:list`, `artisan test`), agent delegation, and the Figma/Playwright
  MCP tools.
- `permissions.ask`: `composer cs-fix`, `git add`, `git commit`, branch
  switching, push, merge, rebase/reset/stash, branch deletion,
  `Edit(./.qwen/**)`, Issue/PR create/edit/comment/merge, compose
  down/restart, migrations, and seeding.
- `permissions.deny`: force-push variants (including `+refspec`, `--mirror`,
  `-f`, `-uf`, `-fu`, and `git -C`/`--git-dir` forms), reads of `.env`
  variants through `Read` and shell, file redirects (`>`, `tee`, `sed -i`),
  and `mcp__playwright__browser_run_code_unsafe`.
- Permission precedence is `deny > ask > allow`; compound shell commands are
  split and evaluated per subcommand with the most restrictive result
  (verified in the installed 0.26.4 build). `ask` is a confirmation prompt,
  not a hard block; `deny` is the only hard rule. A `-y`/yolo run
  auto-approves `ask` rules, so the prompt guard depends on the approval
  mode.
- `Edit(./.qwen/**)` is Qwen's Edit meta-category; it covers `edit`,
  `write_file`, and `notebook_edit`. Arbitrary shell redirects are separately
  denied (`Bash(*>*)`, `*tee *`, `*sed -i*`), so the main session remains the
  configuration writer; this is still a rule layer, not a filesystem sandbox.
- Both MCP servers use `trust: true`. Figma is currently unavailable because
  its subscription expired. Playwright is configured and exposes its browser
  tools; its browser requires a Chrome distribution (`npx playwright install
  chrome`). The 2026-11-01 audit listed the 25 Playwright tools but a browser
  call failed until Chrome was installed. Playwright is pinned to
  `@playwright/mcp@0.0.84`; review the version when upgrading.

Permission rules are the enforced boundary. A subagent's tool allowlist is a
second boundary. Neither replaces the approval gates. `Agent` allow rules are
stripped in Auto Mode, so delegation approval there is classifier-driven.

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
- Context loading is enforced by the result attestation and QA review, not by a
  filesystem sandbox. An agent can still read an unlisted file.
- Reviewer write boundaries are enforceable only by tool removal: product,
  architecture, QA, and security have no `write_file`; QA also blocks `edit`
  and `run_shell_command` and has no Playwright tools. Security and
  architecture run in `plan` mode. QA inspects gate evidence and screenshots;
  the frontend role captures the screenshots and the main session runs the
  gates.
- Forks share the parent's working directory and have no worktree isolation;
  do not use them for parallel writes.
- A subagent's write scope is an instruction, not a filesystem sandbox. The
  orchestrator and frontend role hold write tools; their boundaries are the
  task envelope, the harness ownership table, and review evidence. The
  frontend role inherits the tool set so the Figma and Playwright MCP tools
  are available.
- Nested agents run in the foreground; a nested background launch is rejected.
- Worktree changes must be reviewed and integrated manually; Qwen Code does
  not merge them.
- There is no automatic stage engine. The state machine is enforced by the
  orchestrator prompt, the artifacts, and the human gates, not by code.
- `git add`/`git commit` are `ask` permissions globally, not per-agent. The
  main-session-only commit rule is enforced by the prompt plus the permission
  prompt; specialists can still attempt a commit and will need approval.
  `git checkout`, `git switch`, `git restore`, `git cherry-pick`, and
  `git worktree` are also `ask`.

## Harness Validation

Run the static validator after changing agents, skills, rules, workflow docs,
or settings:

```bash
python3 docs/agent-workflow/validate-harness.py
python3 -m json.tool .qwen/settings.json >/dev/null
git diff --check
```

The validator checks frontmatter, result-contract fields, JSON, and referenced
paths. It does not execute agents; runtime discovery is a separate check with
`qwen --approval-mode plan`.

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
