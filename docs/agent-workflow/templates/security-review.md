# Security Review: Title

```text
Task ID: <issue>-security-<n>
Issue: #<issue> <url>
Branch / commit: <branch> @ <sha>
Author: security-reviewer
Date: <YYYY-MM-DD>
Status: pending | in_progress | blocked | awaiting_approval | completed | failed | rejected
Verdict: pass | pass with findings | fail
```

## Scope

- Feature spec: <path or "not required">
- Technical plan: <path or "not required">
- Diff reviewed: `<base>..<head>`
- Affected domains: <modules>

## Checklist

| Area | Check | Result | Evidence |
| --- | --- | --- | --- |
| Authentication | Intended middleware and order on every route | pass / fail / n/a | <file:line> |
| Authorization | Acting user's right is checked in controller and service | pass / fail / n/a | <file:line> |
| Ownership | Client IDs are checked for ownership and visibility | pass / fail / n/a | <file:line> |
| Admin | Admin actions require an admin user or role | pass / fail / n/a | <file:line> |
| Blocking | Blocked users cannot discover, match, message, or view | pass / fail / n/a | <file:line> |
| Visibility | Hidden/incomplete profiles stay out of discovery | pass / fail / n/a | <file:line> |
| Location | Only approximate location is exposed | pass / fail / n/a | <file:line> |
| Sensitive attributes | Optional, consent-backed, not leaked | pass / fail / n/a | <file:line> |
| Reports | Report details are not shown to the reported user | pass / fail / n/a | <file:line> |
| Uploads | MIME, size, and image dimensions validated | pass / fail / n/a | <file:line> |
| CSRF | Forms use CSRF protection | pass / fail / n/a | <file:line> |
| Input | Mass assignment, raw SQL, redirects reviewed | pass / fail / n/a | <file:line> |
| Logs/events | No secrets or private payloads in logs or broadcasts | pass / fail / n/a | <file:line> |
| Tests | Negative and cross-user cases exist | pass / fail / n/a | <test path> |

## Findings

### <severity>: Title

- Scenario:
- Evidence:
- Impact:
- Recommended correction:

Severity: `blocker` (exploitable or data loss), `major` (needs a fix before
release), `minor` (defense in depth or follow-up).

## Regression risk

- <existing gate or privacy default that could regress>

## Residual risk

- <risk accepted with a reason, or "none">

## Context attestation

```text
Required context:
Files inspected:
Missing context:
Context status: complete | partial | blocked
```

## Approval

```text
Approved by:
Date:
Scope:
Conditions:
Evidence:
```

## Verdict

<Pass, pass with findings, or fail. A `fail` blocks final approval.>
