# Agent Orchestration Contract

This contract defines how a task moves between the main Qwen Code session, the
orchestrator, and the specialist subagents. It is normative: an agent that
skips it is not following the harness.

## Actors

| Actor | Responsibility | May approve |
| --- | --- | --- |
| Main Qwen session | Human-facing entry point; owns `ask_user_question`; records human decisions | Nothing by itself; it relays the human |
| `orchestrator` | Resolves state, delegates, verifies deliverables, advances the stage | Nothing; it escalates |
| Specialist subagents | Produce one deliverable in their role | Nothing |

Top-level named subagents do not receive `ask_user_question`. This is why
every approval is expressed as `awaiting_approval` in the agent result and
handled by the main session.

## Task envelope

The orchestrator sends every delegation with this envelope. A specialist must
return `blocked` if a required field is missing.

```text
Task ID: <issue>-<stage>-<role>-<n>
Agent: <role>
Task type: backend-bugfix | backend-feature | frontend-feature | fullstack-feature | security-fix | docs-harness
Issue: #<number> <url>
Objective: <one paragraph>
Stage: <state-machine stage>
Branch / worktree: <path and branch>
Affected modules: <comma-separated module names>
Required context: <exact file paths, in reading order>
Write scope: <globs the agent may modify>
Read-only scope: <globs the agent may inspect but not modify>
Approved artifacts: <exact paths or "none">
Acceptance criteria: <numbered list, copied from the approved spec>
Gates to run: <commands relevant to this role>
Deliverable: <artifact path or "return in result">
Constraints: <role-specific prohibitions and known risks>
```

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

## Required outputs

Every agent returns the structured result in `artifact-contracts.md`. A result
without `Files inspected`, `Files changed`, and `Verification evidence` is
incomplete and must be rejected by the orchestrator.

Durable artifacts are written only when the task needs them:

| Artifact | Owner | When required |
| --- | --- | --- |
| `feature-spec.md` | product-validator | New user-visible behavior or scope change |
| `technical-plan.md` | architecture-reviewer | New use case, module boundary, data or contract change |
| `design-review.md` | frontend-engineer | UI work with a Figma frame |
| `acceptance-report.md` | qa-engineer | Every non-trivial implementation |
| `security-review.md` | security-reviewer | Security-sensitive change |

A small, low-risk change records "artifact not required" and returns the
content in the result instead.

## Completion, failure, and clarification

- `completed` means the role's deliverable is ready for the next gate, not
  that the feature is approved.
- `failed` means the role could not produce a valid deliverable; include the
  failure and the attempted evidence.
- `rejected` means the deliverable does not meet its criteria; the orchestrator
  sends the findings back to the same role.
- `blocked` means a required input, file, decision, or environment is missing.
- `awaiting_approval` means a human decision is required; the stage does not
  advance.
- `in_progress` and `pending` are used only in interim reporting.

A specialist cannot ask the user a question. It records the question under
`Outstanding questions`, chooses `blocked` when it cannot proceed, and states
the exact decision or data it needs.

## Orchestrator state selection

Before each delegation the orchestrator must be able to point to the evidence
that satisfies the current stage's entry condition. The order is:

1. Verify the Issue and task type.
2. Verify the previous stage's exit evidence.
3. Select the next stage from `state-machine.md`.
4. Confirm the required approvals are recorded.
5. Confirm the write scope does not overlap another active agent.
6. Launch the owner and wait when the next gate depends on it.

If two states are plausible, prefer the earlier gate. If evidence is
ambiguous, return `blocked` instead of guessing.

## Git ownership

- Specialists edit files; they do not commit, push, open pull requests, merge,
  or delete branches.
- The orchestrator or the main session stages and commits on the feature
  branch after QA evidence. The branch must never be `main`.
- Push, pull request, merge, and release are human-approved actions.
- A worktree agent returns its worktree path and branch; the orchestrator
  reviews the diff and integrates it, then re-runs QA.

## Boundaries of the contract

- The contract governs behavior, not filesystem sandboxing. A subagent's tool
  allowlist and `.qwen/settings.json` permissions are the actual enforcement;
  the write scope is an instruction the orchestrator and QA must verify.
- If the installed Qwen Code version lacks a mechanism described here, the
  limitation is recorded in `docs/harness.md` rather than simulated.
