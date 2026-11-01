# Approval Policy

Human approval is a hard gate, not a formality. Agents may prepare drafts and
run local checks autonomously; publishing, merging, deploying, and changing
approved scope require the human.

## What agents may do without approval

- Read files, search the repository, inspect routes, tests, logs, and git state.
- Run read-only commands and the quality gates inside the `php` container.
- Implement inside the approved write scope after the technical plan (and
  Figma design, for UI work) is approved.
- Draft Issues, pull requests, specifications, plans, and review artifacts.
- Stage and commit on the current feature branch after QA evidence.

## Approval matrix

| Action | Approver | Drafted by | Recorded in |
| --- | --- | --- | --- |
| Product scope or acceptance criteria | Human product owner | product-validator | Issue comment or `feature-spec.md` approval block |
| Architecture, schema, contracts, new dependency | Human technical owner | architecture-reviewer | Issue comment or `technical-plan.md` approval block |
| Figma design direction or deviation from the frame | Human product/design owner | frontend-engineer | Issue comment or `design-review.md` |
| GitHub Issue creation or modification | Human | orchestrator or any role | Issue URL plus the approving comment |
| Pull request creation or modification | Human | orchestrator | PR URL plus the approving review/comment |
| Merge to `main` | Human | main session | PR review or merge comment |
| Production deployment | Human | main session | Deployment record |
| Quality-gate bypass | Human | implementer | PR "Risks / Notes" with the failing check and reason |
| Deleting a branch with unmerged work | Human | main session | Issue or PR comment |
| Force push | Forbidden by policy | — | — |

## Escalation protocol

1. The agent stops and returns `awaiting_approval` (or `blocked`) with:
   - the exact decision or action requested
   - the draft artifact path or URL
   - the consequence of each option
   - the evidence already collected
2. The main Qwen session asks the human with `ask_user_question`, quoting the
   request. It does not paraphrase away the risk.
3. The human answer is recorded verbatim in the Issue, PR, or artifact
   approval block.
4. The orchestrator re-reads that record before advancing the stage.

Silence, a previous approval, or a general "continue" does not approve a new
action. Approval is scoped to the action, the artifact version, and the
recipient.

## Approval record

```text
Approved by: <name>
Date: <YYYY-MM-DD>
Scope: <action and artifact version, commit, or URL>
Conditions: <none or explicit conditions>
Evidence: <Issue/PR comment link or artifact path>
```

An approval expires when the approved artifact, scope, or design changes. The
orchestrator must request a new approval after a scope change.

## Quality gates

The quality gates are defined in `.qwen/skills/laravel-definition-of-done` and
`AGENTS.md`. A failing gate blocks the pull request. A bypass is allowed only
with explicit human approval, recorded with the failing command and the reason.

## What is never automatic

- creating or editing a GitHub Issue
- pushing a branch
- opening, editing, or merging a pull request
- deploying to production
- dropping, resetting, or wiping a database
- force pushing
- deleting a branch that contains unmerged work
- changing `.qwen/settings.json` permissions or MCP servers without review

## Tool permissions are separate

This policy describes required human decisions. The enforced boundary is
`.qwen/settings.json` (`permissions.allow`, `ask`, `deny`) plus each
subagent's tool allowlist. A prompt that says "ask first" does not replace a
permission rule, and a permission rule does not replace the workflow gate.
