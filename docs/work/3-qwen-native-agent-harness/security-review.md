# Security Review: Qwen harness remediation (F-01–F-07)

```text
Task ID: 3-security-1
Issue: #3 https://github.com/reture88/freedateapp/issues/3
Branch / commit: 3-qwen-native-agent-harness @ 946e861 + working tree
Author: security-reviewer
Date: 2026-11-01
Status: completed
Verdict: pass with findings
```

## Scope

- `.qwen/settings.json`, all seven `.qwen/agents/*.md`, `.qwen/rules/*.md`
- `AGENTS.md`, `docs/harness.md`, `docs/agent-workflow/*.md`, templates
- `docs/work/3-qwen-native-agent-harness/technical-plan.md`
- No `app/**`, schema, migration, or Composer dependency change

## Checklist

| Area | Check | Result | Evidence |
| --- | --- | --- | --- |
| `.env` Read deny | `Read(...)` meta-category fires, including absolute paths and `grep_search` | pass | live `grep_search` on `.env` denied by `Read(./.env)` |
| `.env` shell deny | Exact `cat` forms for `.env`, `.env.local`, `.env.production`, `.env.testing`, `.env.docker`, `.env.ci`, `.env.backup`; `head`/`tail`/`grep` for the main three/four | pass with findings | `settings.json` deny; `.env.example` is readable; uncommon readers (`printenv`, `cp`, `awk`) remain |
| Reviewer writes | product/architecture/security have only read tools; QA has positive read-only allowlist | pass | `.qwen/agents/*.md` frontmatter |
| QA Playwright | QA has no MCP tools | pass | `qa-engineer.md` `tools: read_file, grep_search, glob, skill` |
| Commit/branch | `git add/commit/checkout/switch/restore/cherry-pick/worktree` and variants are `ask` | pass (prompt-only) | `settings.json` ask; `deny` only for force-push/`.env`/redirects |
| Force push | `--force`, `-f`, `-uf`, `-fu`, `+refspec`, `--mirror`, `git -C`/`-c`/`--git-dir` forms | pass with findings | deny patterns; `GIT_DIR=`, aliases, abbreviated spellings remain |
| Redirect writes | `>`, `tee`, `tee` trailing, `sed -i`, `sed --in-place`; `cp`/`mv`/`install`/`truncate` to `.qwen` | pass with findings | `settings.json` deny; inline interpreters remain |
| MCP | Figma/Playwright config preserved; Playwright pinned; unsafe browser code denied | pass with findings | `settings.json`; `trust: true`; frontend inherits MCP |
| Secrets | No tokens/keys/passwords in committed config | pass | `.qwen/**` scan |
| Policy vs config | Every declared hard boundary is enforced | pass with findings | `deny` rules are hard; `ask` is prompt-only; yolo nullifies ask |

## Findings

### Major — force-push deny is spelling-enumerated

`GIT_DIR=... git push --force`, shell aliases, and abbreviated long options
can miss the anchored deny patterns. The common forms (`--force`, `-f`, `-uf`,
`-fu`, `+refspec`, `--mirror`, `git -C`, `git -c`, `--git-dir=`,
`--work-tree`) are covered, including the no-remote spelling.
**Recommendation:** add a `GIT_DIR=` guard and, if force-push must be
absolute, a wrapper hook; otherwise document that aliases are a residual.

### Major — `.qwen/**` shell writes beyond redirects

`Bash(*>*)`, `*tee*`, `*sed -i*`, `*sed --in-place*`, and `cp`/`mv`/`install`/
`truncate` to `.qwen` are denied, but an inline `perl -i`, `python -c`, or
`ruby -e` write can still target `.qwen/**` through an allow-listed command
prefix. The main session is the documented writer, so this is a
prompt/ownership guard rather than a sandbox.
**Recommendation:** add inline-interpreter guards or accept the residual and
keep the ownership rule.

### Major — Playwright server-wide allow with `trust: true`

`mcp__playwright` is auto-approved and the server is trusted. QA has no MCP
tools; the frontend role is an implementer and legitimately uses them.
`browser_run_code_unsafe` is denied. The remaining tools can write screenshots
and upload local files, but not source/config.
**Recommendation:** keep the frontend scope; re-review when the pinned
`@playwright/mcp@0.0.84` is upgraded.

### Minor

- `.env` readers outside `Read`/the exact shell shapes (`printenv`, `cp`,
  `awk`) remain.
- `Bash(*>*)` over-blocks some legitimate read-only commands; agents may
  switch to unblocked spellings.
- Orchestrator/frontend hold `write_file` under `auto-edit`; the `.qwen/**`
  exclusion is prompt-only.
- `validate-harness.py` does not assert reviewer tool sets or deny coverage.
- QA has no shell, so it inspects gate evidence rather than running it.
- The approval and envelope evidence is kept in this work directory; the
  Issue and artifact links are authoritative.

## Evidence

- `docs/work/3-qwen-native-agent-harness/technical-plan.md`
- `docs/work/3-qwen-native-agent-harness/orchestrator-envelope.md`
- `.qwen/settings.json` and the seven agent definitions

## Residual risk

- `ask` is a confirmation prompt; a `-y`/yolo run auto-approves it. The hard
  rules are `deny` only.
- MCP tool results are untrusted input; `trust: true` means no per-call prompt.
- Figma connectivity is excluded (expired subscription; not a harness defect).

## Context attestation

```text
Required context: AGENTS.md; .qwen/settings.json; .qwen/agents/*.md; .qwen/rules/*.md; docs/agent-workflow/approval-policy.md; docs/agent-workflow/concurrency-policy.md; docs/agent-workflow/state-machine.md; docs/agent-workflow/artifact-contracts.md; docs/agent-workflow/templates/security-review.md; docs/harness.md; docs/work/3-qwen-native-agent-harness/technical-plan.md
Files inspected: AGENTS.md; .qwen/settings.json; all seven .qwen/agents/*.md; all three .qwen/rules/*.md; docs/agent-workflow/approval-policy.md; docs/agent-workflow/concurrency-policy.md; docs/agent-workflow/state-machine.md; docs/agent-workflow/artifact-contracts.md; docs/agent-workflow/templates/security-review.md; docs/harness.md; docs/work/3-qwen-native-agent-harness/technical-plan.md; docs/freedateapp-technical-spec.md; docs/freedateapp-product-scope.md; docs/architektura.md; .qwen/skills/laravel-cqrs-conventions/SKILL.md
Missing context: none
Context status: complete
```

## Verdict

**Pass with findings.** No blocker; the configuration is a meaningful
improvement over the audited baseline. The remaining gaps are documented and
do not change application behavior. A fresh QA pass is still required.
