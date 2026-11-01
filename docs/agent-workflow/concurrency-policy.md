# Concurrency Policy

Parallel work is allowed only when the tasks are independent and the write
scopes do not overlap. The default is sequential.

## Ownership

| Work area | Owner | Paths |
| --- | --- | --- |
| Domain, CQRS, persistence | backend-engineer | `app/src`, `app/database`, `app/routes` |
| Framework glue and tests | backend-engineer | `app/app` (except Livewire/Filament), `app/tests`, `app/config` |
| Blade, Livewire, Filament, CSS | frontend-engineer | `app/resources/views`, `app/app/Livewire`, `app/app/Filament`, `app/public/css` |
| Specification | product-validator (author); orchestrator writes | `docs/work/<issue>-<slug>/feature-spec.md` |
| Technical plan | architecture-reviewer (author); orchestrator writes | `docs/work/<issue>-<slug>/technical-plan.md` |
| Acceptance report | qa-engineer (author); orchestrator writes | `docs/work/<issue>-<slug>/acceptance-report.md` |
| Security review | security-reviewer (author); orchestrator writes | `docs/work/<issue>-<slug>/security-review.md` |
| Design review | frontend-engineer (author); orchestrator writes | `docs/work/<issue>-<slug>/design-review.md` |
| Git history, integration | main session | branch, commit, worktree merge |

One file has one writer at a time. Reviewers and planners return artifact text;
they do not write the file. A reviewer never edits the files it reviews.

## Harness ownership

The main session is the only writer of Qwen configuration. There is no
unrestricted harness-writer role.

| Area | Writer | Required review |
| --- | --- | --- |
| `AGENTS.md`, `docs/harness.md`, `docs/agent-workflow/**` | orchestrator, `docs-harness` task only | architecture-reviewer |
| `docs/agent-workflow/templates/**` | orchestrator, `docs-harness` task only | architecture-reviewer |
| `.qwen/agents/**` | main session | architecture-reviewer + security-reviewer for tool permissions |
| `.qwen/skills/**`, `.qwen/rules/**` | main session | architecture-reviewer |
| `.qwen/settings.json`, MCP servers, permissions | main session | security-reviewer + human approval |
| `app/src`, `app/database`, `app/routes`, `app/tests` | backend-engineer | qa-engineer |
| `app/resources/views`, `app/app/Livewire`, `app/app/Filament`, `app/public/css` | frontend-engineer | qa-engineer |

`Edit(./.qwen/**)` is an `ask` permission, so a configuration write is visible
even when the writer is the main session. The orchestrator never writes
`.qwen/**`; it returns the proposed configuration change to the main session.

## Parallel-safe combinations

| Combination | Parallel? | Condition |
| --- | --- | --- |
| Product validation + architecture exploration | Yes | Exploratory only; architecture does not write the plan until the spec is approved |
| Backend + frontend implementation | Yes | Disjoint paths; separate worktrees; a documented API/command contract |
| QA + security review | Yes | Read-only on application code; each returns its artifact text; the orchestrator writes the files after integration |
| Two backend tasks | Only with disjoint modules and files | Separate worktrees; no shared migration |
| Two frontend tasks | Only with disjoint views/components | Separate worktrees; no shared CSS block |
| Any pair writing the same file | No | Sequence them |

Maximum two parallel writers. More than that multiplies integration cost
without speeding the gate.

## Worktrees in Qwen Code

Verified in the installed version (0.26.4):

- A named subagent can be launched with `isolation: "worktree"`. Qwen Code
  creates a worktree under `.qwen/worktrees/agent-<7hex>`, removes it if the
  agent made no changes, and otherwise returns the worktree path and branch.
- A named subagent can be pinned to an existing caller-owned worktree with
  `working_dir`. That launch is foreground; the caller owns cleanup.
- Forks share the parent's working directory and have no worktree isolation.
  Do not use forks for parallel writes.
- Nested agents run in the foreground. A background launch is rejected inside
  a nested delegation.

Rules:

1. Parallel writers get one worktree each.
2. The orchestrator reviews each returned diff; the main session integrates
   and commits.
3. Integration happens on the feature branch, never on `main`.
4. After integration, re-run the quality gates and QA on the combined result.
5. Do not delete a worktree branch until its changes are merged and verified.
6. `.qwen/worktrees/` is git-ignored so worktree noise does not enter
   `git status` during integration.

## Git rules

- The feature branch is `<issue>-<slug>`, created from up-to-date `main`.
- The **main session** is the only commit owner.
- Specialists and the orchestrator do not commit, push, switch branches,
  rebase, or merge. They return changes or a commit proposal.
- `git add`, `git commit`, `git checkout`, `git switch`, `git restore`,
  `git cherry-pick`, `git worktree`, and the `git -C`/`-c`/`--git-dir`/
  `--work-tree` forms are `ask` permissions; they are not silently
  auto-approved in a normal run. `ask` is a confirmation prompt, not a hard
  block; `deny` is the only hard rule, and a `-y`/yolo run auto-approves
  `ask`.
- Never commit to `main`.
- Never force push. Sync by merging `main` into the branch.
- Before delegating a writer, check `git status`. If the working tree has
  unrelated changes, record them and keep them out of the change set.
- Do not use `git checkout --`, `git reset --hard`, `git clean`, or
  `git stash drop` while another agent's work is uncommitted.

## Conflict handling

If two agents touched the same file:

1. Stop the integration.
2. Identify which change belongs to which task.
3. Resolve manually or re-delegate one change to a fresh worktree.
4. Re-run the gates and QA.
5. Record the conflict and the resolution in the Issue or PR.

Never resolve a conflict by discarding the other agent's work.

## Integration review

Before combining parallel work, the orchestrator checks:

- every changed file is inside its owner's write scope
- no file was changed by two agents
- each result has a complete context attestation
- the command/query or API contract matches on both sides
- migrations and routes do not collide
- tests cover the integrated behavior
- the combined diff passes `git diff --check`

Only then does QA review the integrated change. The main session performs the
commit.
