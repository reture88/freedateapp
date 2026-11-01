# Agent Workflow

Normative policy for the Qwen Code agent harness.

| Document | Purpose |
| --- | --- |
| `orchestration.md` | Task envelope, delegation, outputs, escalation, git ownership |
| `state-machine.md` | Stages, conditional paths, transition evidence, security overlay |
| `approval-policy.md` | Human approval matrix, escalation, what is never automatic |
| `context-loading.md` | Required, conditional, and forbidden context per role |
| `concurrency-policy.md` | Ownership, parallel matrix, worktrees, conflict handling |
| `artifact-contracts.md` | Result envelope, statuses, artifact storage, evidence standards |

Templates live in `templates/`. Agent definitions live in `.qwen/agents/`.
Standing code rules live in `.qwen/skills/`. Path-gated pointers live in
`.qwen/rules/`.

Start with `orchestration.md` and `state-machine.md`; the rest are referenced
from there.
