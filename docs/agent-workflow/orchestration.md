# Agent Orchestration Contract

This contract defines how a task moves between the main Qwen Code session, the
orchestrator, and the specialist subagents. It is normative: an agent that
skips it is not following the harness.

## Actors

| Actor | Responsibility | May approve |
| --- | --- | --- |
| Main Qwen session | Human-facing entry point; owns `ask_user_question`; records human decisions; sole commit owner | Nothing by itself; it relays the human |
| `orchestrator` | Resolves state, delegates, verifies deliverables, advances the stage; writes task artifacts and harness docs for `docs-harness` | Nothing; it escalates |
| Specialist subagents | Produce one deliverable in their role; read-only unless the role owns code | Nothing |

Top-level named subagents do not receive `ask_user_question`. This is why
every approval is expressed as `awaiting_approval` in the agent result and
handled by the main session.

## Task envelope

The orchestrator sends every delegation with this envelope. A specialist must
return `blocked` if a required field is missing.

```text
Task ID: <issue>-<stage>-<n>
Agent: <role>
Task type: backend-bugfix | backend-feature | frontend-feature | fullstack-feature | security-fix | docs-harness
Issue: #<number> <url>
Objective: <one paragraph>
Stage: <state-machine stage>
Branch / worktree: <path and branch>
Affected modules: <comma-separated module names>
Required context: <exact file paths, in reading order>
Write scope: <globs the agent may modify, or "none">
Read-only scope: <globs the agent may inspect but not modify>
Approved artifacts: <exact paths or "none">
Acceptance criteria: <numbered list, copied from the approved spec>
Gates to run: <commands relevant to this role>
Deliverable: <artifact path or "return in result">
Constraints: <role-specific prohibitions and known risks>
```

`Task type` must match the Issue's `Task type` field. If the field is missing,
the orchestrator returns `blocked`; it does not infer a type from the title.

## Delegation rules

1. One owner per work area. The orchestrator chooses exactly one role for a
   deliverable.
2. The orchestrator supplies context paths instead of asking the specialist to
   guess. The specialist may read additional files inside its read-only scope.
3. Use `run_in_background: false` when the next gate depends on the result.
4. Use the parallel matrix in `concurrency-policy.md`. Never launch two writers
   over overlapping paths.
5. The orchestrator does not rewrite a specialist's deliverable. It accepts,
   rejects with findings, or re-delegates.
6. The orchestrator must not approve a gate on the specialist's behalf.
7. Every result must include the context attestation: `Required context`,
   `Files inspected`, `Missing context`, `Context status`.

## Required outputs

Every agent returns the exact structured result in `artifact-contracts.md`. A
result missing any mandatory field — `Required context`, `Files inspected`,
`Missing context`, `Context status`, `Files changed`, `Verification evidence`,
or another envelope field — is incomplete and must be rejected by the
orchestrator.

Durable artifacts are written only when the task needs them. The
**orchestrator writes the artifact**; the author returns the full text in its
result:

| Artifact | Author | When required |
| --- | --- | --- |
| `feature-spec.md` | product-validator | New user-visible behavior or scope change |
| `technical-plan.md` | architecture-reviewer | New use case, module boundary, data or contract change |
| `design-review.md` | frontend-engineer | UI work with a Figma frame |
| `acceptance-report.md` | qa-engineer | Every non-trivial implementation |
| `security-review.md` | security-reviewer | Security-sensitive change |

A small, low-risk change records "artifact not required" and returns the
content in the result instead.

## Context gate

A specialist may return `completed` only when `Context status: complete` and
`Missing context` is `none` or contains only conditional items marked not
applicable with a reason. A mandatory path that was not read means
`Context status: partial` when a usable deliverable is returned, or `blocked`
when delivery is impossible; `Status` cannot be `completed`. The orchestrator
and QA apply the same rule. See `context-loading.md`.

## Completion, failure, and clarification

- `completed` means the role's deliverable is ready for the next gate, not
  that the feature is approved.
- `failed` means the role could not produce a valid deliverable; include the
  failure and the attempted evidence.
- `rejected` means the deliverable does not meet its criteria; the orchestrator
  sends the findings back to the same role.
- `blocked` means a required input, file, decision, environment, or context
  item is missing.
- `awaiting_approval` means a human decision is required; the stage does not
  advance.
- `in_progress` and `pending` are used only in interim reporting.

A specialist cannot ask the user a question. It records the question under
`Outstanding questions`, chooses `blocked` when it cannot proceed, and states
the exact decision or data it needs.

## Orchestrator state selection

Before each delegation the orchestrator must be able to point to the evidence
that satisfies the current stage's entry condition. The order is:

1. Verify the Issue and its `Task type` field.
2. Verify the previous stage's exit evidence and context attestation.
3. Select the next stage from `state-machine.md`.
4. Confirm the required approvals are recorded.
5. Confirm the write scope does not overlap another active agent.
6. Launch the owner and wait when the next gate depends on it.

If two states are plausible, prefer the earlier gate. If evidence is
ambiguous, return `blocked` instead of guessing.

## Git ownership

- The **main session** is the only commit owner. It stages, commits, pushes,
  and integrates.
- Implementers edit code or UI files. Reviewers and planners are read-only and
  return artifact text; the orchestrator writes the artifact. For a
  `docs-harness` task, the orchestrator also writes `AGENTS.md`,
  `docs/harness.md`, and `docs/agent-workflow/**`; the main session writes
  `.qwen/**` configuration.
- Specialists and the orchestrator do not `git add`, `git commit`,
  `git push`, switch branches, merge, or delete branches.
- The orchestrator may run read-only git commands and return a commit proposal
  (changed files, message, evidence) to the main session.
- Push, pull request, merge, and release are human-approved actions.
- A worktree agent returns its worktree path and branch; the orchestrator
  reviews the diff, the main session integrates and commits, then QA re-runs.

## Harness ownership

The main session owns `.qwen/settings.json`, `.qwen/agents/**`,
`.qwen/skills/**`, `.qwen/rules/**`, and MCP configuration. For a
`docs-harness` task, the orchestrator may write `AGENTS.md`,
`docs/harness.md`, and `docs/agent-workflow/**`. It never writes `.qwen/**`.
See `concurrency-policy.md`.

## Boundaries of the contract

- The contract governs behavior, not filesystem sandboxing. A subagent's tool
  allowlist and `.qwen/settings.json` permissions are the actual enforcement;
  the write scope is an instruction the orchestrator and QA must verify.
- If the installed Qwen Code version lacks a mechanism described here, the
  limitation is recorded in `docs/harness.md` rather than simulated.
