---
description: Harness maintenance rules for .qwen and agent-workflow changes
paths:
  - '.qwen/**'
  - 'docs/agent-workflow/**'
  - 'AGENTS.md'
  - 'docs/harness.md'
---

Before changing the harness:

- Read `docs/harness.md` and the relevant `docs/agent-workflow/*.md` file.
- Keep one authoritative source: skills in `.qwen/skills`, agents in
  `.qwen/agents`, path-gated activation in `.qwen/rules`, workflow policy in
  `docs/agent-workflow`.
- Do not duplicate rule text across those layers; link to the owner.
- Preserve the Figma and Playwright MCP servers and the human approval gates.
- Validate agent frontmatter, JSON, referenced paths, and
  `git diff --check` after the change.
- Record the Qwen Code version when documenting a capability.
