# Workflow State Machine

A feature or bug moves through stages. A stage advances only on recorded
evidence, never because an agent says it is finished. The orchestrator owns the
transition decision; the main session records human approvals.

## Stages

| Stage | Entry evidence | Exit evidence | Owner |
| --- | --- | --- | --- |
| Proposed | GitHub Issue with title, context, scope, affected modules | Task type, affected modules, and acceptance criteria are known | Main session |
| Product validation | Issue and task type | `feature-spec.md` with testable criteria and risks | product-validator |
| Awaiting product approval | Complete feature spec | Recorded human approval | Main session |
| Architecture planning | Approved feature spec | `technical-plan.md` with options, boundaries, data/contract impact, phases | architecture-reviewer |
| Awaiting architecture approval | Complete technical plan | Recorded human approval | Main session |
| Design | Approved plan and a Figma frame link | `design-review.md` with frame, states, desktop/mobile comparison | frontend-engineer |
| Backend implementation | Approved plan; approved design if UI-coupled | Changed backend files, tests, Definition of Done evidence, updated domain context | backend-engineer |
| Frontend implementation | Approved plan and design | Changed UI files, desktop/mobile screenshots, gates | frontend-engineer |
| QA | Implementation result with evidence | `acceptance-report.md` with a pass verdict or actionable findings | qa-engineer |
| Security review | Diff and affected-domain list | `security-review.md` with a verdict | security-reviewer |
| Awaiting final approval | QA pass; security pass when required; PR prepared | Recorded human approval | Main session |
| Ready for release | Final approval and green gates | Merge evidence | Main session |
| Released | Merged PR, branch deleted | Release note or Issue closure | Main session |

`awaiting_approval` is an agent result status, not a stage. When an agent
returns it, the current stage stays where it is.

## Conditional paths

The full chain is the default for `fullstack-feature`. Short paths are allowed
only with the reasons recorded in the Issue.

| Task type | Required stages | Optional or skipped |
| --- | --- | --- |
| `backend-bugfix` | Proposed → Architecture planning (light) → Backend implementation → QA → Awaiting final approval → Ready for release | Product validation and design are skipped when product scope and UI do not change; record why |
| `backend-feature` | Proposed → Product validation → Awaiting product approval → Architecture planning → Awaiting architecture approval → Backend implementation → QA → Awaiting final approval → Ready for release | Design when no user-visible UI changes |
| `frontend-feature` | Proposed → Product validation → Awaiting product approval → Architecture planning (light) → Awaiting architecture approval → Design → Frontend implementation → QA → Awaiting final approval → Ready for release | Backend implementation when no backend change |
| `fullstack-feature` | All stages | None by default |
| `security-fix` | Proposed → Architecture planning (light) → Backend implementation → QA → Security review → Awaiting final approval → Ready for release | Product validation when behavior is unchanged; design when no UI change |
| `docs-harness` | Proposed → Architecture planning (harness) → Backend implementation (docs/harness files) → QA (document and config validation) → Awaiting final approval → Ready for release | Product validation, design, and security review unless permissions, MCP, or public behavior change |

## Security overlay

Security review is mandatory, regardless of task type, when the change touches:

- authentication, registration, verification, password reset, or account state
- authorization, middleware order, ownership checks, or admin actions
- profile visibility, discovery eligibility, or blocking
- exact or approximate location
- reports, blocks, moderation, or audit logs
- chat or assistant content that can expose profile data
- uploads, file storage, or public URLs

The security stage runs after implementation and QA, before final approval. A
security `fail` blocks the transition.

## Transition rules

1. **Evidence, not assertion.** Each exit condition names an artifact or a
   command output. A summary alone is not evidence.
2. **No skipped gates.** A skipped stage is recorded with the task type and a
   one-line reason. The orchestrator may not skip a mandatory stage because
   the change "looks small".
3. **Approval is per action.** Product approval does not approve architecture;
   architecture approval does not approve the PR; PR approval does not approve
   a merge to production.
4. **Scope change resets the gate.** If implementation needs a product or
   architecture change, return to the relevant approval stage. Do not absorb
   it silently.
5. **QA after integration.** Parallel backend and frontend work is QA'd after
   the changes are integrated, not only per branch.
6. **Failure loops.** `failed` or `rejected` sends the work back to the same
   owner with findings. Two consecutive failures on the same criterion
   escalate to the human.
7. **Blocked stops the chain.** A missing file, decision, environment, or
   approval stops the workflow; it does not trigger a guess.

## Recording state

Record the stage, the evidence, and the approval in the GitHub Issue (or the
task artifact) so the state survives a session restart. The orchestrator reads
that record before its first delegation; a previous chat is not the source of
truth.
