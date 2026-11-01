# Artifact and Result Contracts

Every agent returns the same result envelope. Durable artifacts use the same
header and status vocabulary. This makes the orchestrator's checks mechanical.

## Result envelope

```text
Task ID: <issue>-<stage>-<n>
Agent role: <role>
Status: pending | in_progress | blocked | awaiting_approval | completed | failed | rejected
Summary: <what was done or found, 1-5 sentences>
Required context: <the mandatory paths for this role, or "none">
Files inspected: <paths actually read, or "none">
Missing context: <mandatory path not read and why, or "none">
Context status: complete | partial | blocked
Files changed: <paths, or "none">
Decisions made: <decision and reason, or "none">
Outstanding questions: <question and needed decision, or "none">
Risks: <risk and impact, or "none">
Verification evidence: <commands, results, links, screenshot paths, or "none">
Next recommended action: <the next stage or the exact escalation>
```

Role-specific fields (`Product verdict`, `Architecture verdict`, `Verdict`,
`Acceptance criteria`, `Figma frame`, `Scope reviewed`) go immediately after
`Summary`. They do not replace any mandatory field.

### Field rules

- `Task ID` is `<issue>-<stage>-<n>` and is stable for the delegation; reuse
  it in follow-ups. `<stage>` identifies the role's stage; `<n>` is the
  delegation sequence, not a per-file counter.
- `Status` uses only the seven values above, in every document.
- `Required context` lists the mandatory paths from the role's
  `context-loading` row and its agent definition.
- `Files inspected` lists the files actually read, not a directory guess.
- `Missing context` is `none` when every mandatory path was read. It may list
  a conditional item that is explicitly marked not applicable, with the
  reason.
- `Context status` is `complete` when every mandatory path was read and
  `Missing context` is `none` or contains only conditional items marked not
  applicable with a reason; `partial` when a mandatory path is missing but a
  usable deliverable is returned; `blocked` when the missing mandatory path
  prevents delivery.
- `Files changed` must match the agent's diff. Reviewers and planners report
  `none`; they return artifact text for the orchestrator to write.
- `Decisions made` records a choice that constrains later work, with its
  reason.
- `Outstanding questions` is where a subagent puts what it would have asked
  the human.
- `Verification evidence` is not a claim: include the command and the
  decisive output, the test count, the Issue/PR link, or the screenshot path.
- If a field does not apply, write `none`; do not omit it.

## Context attestation

An agent may return `completed` only when:

1. `Required context` lists the mandatory paths for its role.
2. `Files inspected` includes every one of them.
3. `Missing context` is `none` (or a conditional item is marked not
   applicable with a reason).
4. `Context status` is `complete`.

A missing mandatory path means `Context status: partial` when a usable
deliverable is returned, or `blocked` when delivery is impossible; the result
cannot be `completed`. QA rejects any result that omits these fields or claims
`complete` without the matching `Files inspected` evidence. The orchestrator
applies the same check before it accepts a specialist result.

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

## Verdict to transition

| Role verdict | Next transition |
| --- | --- |
| `product-validator`: `fits scope` | Architecture planning |
| `product-validator`: `scope change` | Awaiting product approval with the changed criteria |
| `product-validator`: `duplicate` or `rejected` | Stop; record the reason |
| `architecture-reviewer`: `fits` | Awaiting architecture approval |
| `architecture-reviewer`: `fits with scope change` | Product re-validation, then architecture approval |
| `architecture-reviewer`: `needs product re-validation` | Product validation |
| `architecture-reviewer`: `rejected` | Stop; record the reason |
| `qa-engineer`: `pass` | Security review when required, otherwise final approval |
| `qa-engineer`: `partial` or `fail` | Return to the implementing role with findings |
| `security-reviewer`: `pass` or `pass with findings` | Final approval |
| `security-reviewer`: `fail` | Return to the implementing role with findings |
| `design-review`: `pass` | QA |
| `design-review`: `pass with gaps` | QA with the documented gaps |
| `design-review`: `fail` | Frontend implementation with findings |

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
- The **orchestrator** writes artifacts. Product, architecture, design, QA,
  and security roles return the artifact text in their result; they are
  read-only.
- Each artifact starts with the Task ID, owner, date, status, and the
  approval block when approved.
- A changed artifact must be re-approved if the change affects its scope.
- Do not commit a durable artifact for a trivial change. Record "artifact not
  required" in the result and keep the content there.
- Do not paste secrets, tokens, full environment files, or personal data into
  an artifact.

Templates live in `docs/agent-workflow/templates/`.

## Artifact header

```text
Task ID: <issue>-<stage>-<n>
Issue: #<issue> <url>
Author: <role>
Date: <YYYY-MM-DD>
Status: pending | in_progress | blocked | awaiting_approval | completed | failed | rejected
```

`completed` with an approved scope means the artifact is final; the approval
block is still required.

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
| Context was loaded | `Required context`, matching `Files inspected`, and `Context status: complete` |

A summary such as "tests pass" without the command output is not evidence.

## Handoff

The orchestrator passes the previous stage's artifact path and the relevant
result fields into the next task envelope. A specialist does not need the whole
prior transcript; it needs the approved artifact, the acceptance criteria, the
required context, and the write scope.
