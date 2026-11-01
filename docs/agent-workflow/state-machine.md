# Workflow State Machine

A feature or bug moves through stages. A stage advances only on recorded
evidence, never because an agent says it is finished. The orchestrator owns the
transition decision; the main session records human approvals.

## Stages

| Stage | Entry evidence | Exit evidence | Owner |
| --- | --- | --- | --- |
| Proposed | GitHub Issue with title, task type, context, scope, affected modules | Task type, affected modules, and acceptance criteria are known | Main session |
| Product validation | Issue and task type | `feature-spec.md` with testable criteria and risks | product-validator |
| Awaiting product approval | Complete feature spec | Recorded human approval | Main session |
| Architecture planning | Approved feature spec | `technical-plan.md` with options, boundaries, data/contract impact, phases | architecture-reviewer |
| Awaiting architecture approval | Complete technical plan | Recorded human approval | Main session |
| Design | Approved plan and a Figma frame link | `design-review.md` with frame, states, desktop/mobile comparison | frontend-engineer |
| Backend implementation | Approved plan; approved design if UI-coupled | Changed backend files, tests, Definition of Done evidence, updated domain context | backend-engineer |
| Frontend implementation | Approved plan and design | Changed UI files, desktop/mobile screenshots, gates | frontend-engineer |
| Implementation (harness) | Approved harness plan | Harness docs/config changed; validation evidence | orchestrator / main session |
| QA | Implementation result with context attestation and evidence | `acceptance-report.md` with a pass verdict or actionable findings | qa-engineer |
| Security review | Diff, context attestation, and affected-domain list | `security-review.md` with a verdict | security-reviewer |
| Awaiting final approval | QA pass; security pass when required; PR prepared | Recorded human approval | Main session |
| Ready for release | Final approval and green gates | Merge evidence | Main session |
| Released | Merged PR, branch deleted | Release note or Issue closure | Main session |

`awaiting_approval` is an agent result status, not a stage. When an agent
returns it, the current stage stays where it is.

## Task types

The canonical task types are:

```text
backend-bugfix | backend-feature | frontend-feature | fullstack-feature | security-fix | docs-harness
```

The task type is recorded in the GitHub Issue's `Task type` field. The
orchestrator returns `blocked` if it is missing or does not match the Issue.
The title prefix (`[BE]`, `[FE]`, `[QA]`, `[FEBE]`) is a routing hint, not the
task type.

## Conditional paths

The full chain is the default for `fullstack-feature`. Short paths are allowed
only with the reasons recorded in the Issue. Every path that reaches
implementation includes **Awaiting architecture approval**; a “light” or
“harness” plan reduces the plan's depth, not the gate.

| Task type | Required stages | Optional or skipped |
| --- | --- | --- |
| `backend-bugfix` | Proposed → Architecture planning (light) → Awaiting architecture approval → Backend implementation → QA → Awaiting final approval → Ready for release | Product validation and design are skipped when product scope and UI do not change; record why |
| `backend-feature` | Proposed → Product validation → Awaiting product approval → Architecture planning → Awaiting architecture approval → Backend implementation → QA → Awaiting final approval → Ready for release | Design when no user-visible UI changes |
| `frontend-feature` | Proposed → Product validation → Awaiting product approval → Architecture planning (light) → Awaiting architecture approval → Design → Frontend implementation → QA → Awaiting final approval → Ready for release | Backend implementation when no backend change |
| `fullstack-feature` | All stages | None by default |
| `security-fix` | Proposed → Architecture planning (light) → Awaiting architecture approval → Backend implementation → QA → Security review → Awaiting final approval → Ready for release | Product validation when behavior is unchanged; design when no UI change |
| `docs-harness` | Proposed → Architecture planning (harness) → Awaiting architecture approval → Implementation (harness) → QA → Awaiting final approval → Ready for release | Product validation and design; security review when permissions, MCP, or public behavior changes |

### Light and harness plans

- A **light plan** uses `technical-plan.md` with the affected modules,
  options, data/contract impact, risks, and a one-line `Light plan` note. It
  still requires recorded human approval before implementation.
- A **harness plan** uses the same artifact and covers the affected harness
  files, validation commands, and rollback. It still requires recorded human
  approval before implementation.
- `docs-harness` implementation ownership: the orchestrator writes
  `AGENTS.md`, `docs/harness.md`, and `docs/agent-workflow/**`; the main
  session writes `.qwen/**` configuration. See
  `docs/agent-workflow/concurrency-policy.md`.

## Security overlay

Security review is mandatory, regardless of task type, when the change touches:

- authentication, registration, verification, password reset, or account state
- authorization, middleware order, ownership checks, or admin actions
- profile visibility, discovery eligibility, or blocking
- exact or approximate location
- reports, blocks, moderation, or audit logs
- chat or assistant content that can expose profile data
- uploads, file storage, or public URLs
- `.qwen/settings.json`, MCP servers, or agent tool permissions

The security stage runs after implementation and QA, before final approval. A
security `fail` blocks the transition.

## Transition rules

1. **Evidence, not assertion.** Each exit condition names an artifact or a
   command output. A summary alone is not evidence.
2. **Architecture approval is never silent.** Implementation starts only after
   `Awaiting architecture approval` is recorded, including for light and
   harness plans. A skipped stage is recorded with the task type and a one-line
   reason; the orchestrator may not skip it because the change “looks small”.
3. **Context attestation is required.** QA rejects an implementation result
   whose `Context status` is not `complete` or whose required context is
   missing. See `artifact-contracts.md`.
4. **Approval is per action.** Product approval does not approve architecture;
   architecture approval does not approve the PR; PR approval does not approve
   a merge to production.
5. **Scope change resets the gate.** If implementation needs a product or
   architecture change, return to the relevant approval stage. Do not absorb
   it silently.
6. **QA after integration.** Parallel backend and frontend work is QA'd after
   the changes are integrated, not only per branch.
7. **Failure loops.** `failed` or `rejected` sends the work back to the same
   owner with findings. Two consecutive failures on the same criterion
   escalate to the human.
8. **Blocked stops the chain.** A missing file, decision, environment, or
   approval stops the workflow; it does not trigger a guess.

## Recording state

The **main session** records the stage, evidence, and approval in the GitHub
Issue (or the task artifact), because named subagents cannot create or comment
on an Issue without approval and do not receive `ask_user_question`. The
orchestrator returns the proposed record and `Status: awaiting_approval`; the
main session writes it after asking the human. A previous chat is not the
source of truth.
