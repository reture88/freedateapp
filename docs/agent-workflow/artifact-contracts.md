# Artifact and Result Contracts

Every agent returns the same result envelope, and durable artifacts use the
same structure. This makes the orchestrator's checks mechanical.

## Result envelope

```text
Task ID: <issue>-<stage>-<role>-<n>
Agent role: <role>
Status: pending | in_progress | blocked | awaiting_approval | completed | failed | rejected
Summary: <what was done or found, 1-5 sentences>
Files inspected: <paths, or "none">
Files changed: <paths, or "none">
Decisions made: <decision and reason, or "none">
Outstanding questions: <question and needed decision, or "none">
Risks: <risk and impact, or "none">
Verification evidence: <commands, results, links, screenshot paths, or "none">
Next recommended action: <the next stage or the exact escalation>
```

### Field rules

- `Task ID` is stable for the whole run; reuse it in every follow-up.
- `Status` uses only the seven values above.
- `Files inspected` must list the files actually read, not a directory guess.
- `Files changed` must match the agent's diff. Reviewers usually report
  `none`.
- `Decisions made` records a choice that constrains later work, with its
  reason.
- `Outstanding questions` is where a subagent puts what it would have asked
  the human.
- `Verification evidence` is not a claim: include the command and the
  decisive output, the test count, the Issue/PR link, or the screenshot path.
- If a field does not apply, write `none`; do not omit it.

## Status meanings

| Status | Meaning | Orchestrator action |
| --- | --- | --- |
| `pending` | Accepted, not started | Wait or re-check prerequisites |
| `in_progress` | Work underway; interim only | Wait |
| `blocked` | Missing input, file, environment, or decision | Resolve or escalate |
| `awaiting_approval` | A human gate is reached | Ask the human, record the answer |
| `completed` | Deliverable ready for the next gate | Verify evidence, advance |
| `failed` | Could not produce a valid deliverable | Inspect the failure, re-delegate or escalate |
| `rejected` | Deliverable does not meet criteria | Return findings to the same owner |

`completed` does not mean approved, merged, or released.

## Durable artifacts

Store a durable artifact only when the workflow needs it:

```text
docs/work/<issue>-<slug>/feature-spec.md
docs/work/<issue>-<slug>/technical-plan.md
docs/work/<issue>-<slug>/design-review.md
docs/work/<issue>-<slug>/acceptance-report.md
docs/work/<issue>-<slug>/security-review.md
```

- `<issue>` is the GitHub Issue number; `<slug>` is the branch slug.
- Each artifact starts with the Task ID, owner, date, status, and the
  approval block when approved.
- A changed artifact must be re-approved if the change affects its scope.
- Do not commit a durable artifact for a trivial change. Record "artifact not
  required" in the result and keep the content there.
- Do not paste secrets, tokens, full environment files, or personal data into
  an artifact.

Templates live in `docs/agent-workflow/templates/`.

## Approval block

```text
Approved by: <name>
Date: <YYYY-MM-DD>
Scope: <action and artifact version or commit>
Conditions: <none or explicit conditions>
Evidence: <Issue/PR comment link or artifact path>
```

An artifact without an approval block is a draft.

## Evidence standards

| Claim | Acceptable evidence |
| --- | --- |
| Tests pass | `docker compose exec php composer test` output with the test count |
| Static analysis is clean | `composer phpstan` output ending `[OK] No errors` |
| Style is clean | `composer cs-check` with no diff |
| Route exists | `php artisan route:list` output containing the route |
| UI matches the design | desktop and mobile screenshots plus the Figma frame reference |
| Change is scoped | `git diff --name-only` and `git diff --check` |
| Issue/PR exists | URL and, for approval, the approving comment |

A summary such as "tests pass" without the command output is not evidence.

## Handoff

The orchestrator passes the previous stage's artifact path and the relevant
result fields into the next task envelope. A specialist does not need the whole
prior transcript; it needs the approved artifact, the acceptance criteria, and
the write scope.
