---
description: Frontend and visual-verification context for Freedateapp UI changes
paths:
  - 'app/resources/views/**'
  - 'app/app/Livewire/**'
  - 'app/app/Filament/**'
  - 'app/public/css/**'
---

Before editing UI under these paths:

- Read `.qwen/skills/livewire-blade-conventions/SKILL.md` and
  `.qwen/skills/figma-playwright-verification/SKILL.md` if they are not
  already in context.
- Read the linked Figma frame through Figma MCP before changing layout or
  styling. If no frame is linked, use
  `docs/freedateapp-figma-design-brief.md` only after the human confirms that
  no frame exists.
- Keep the `mtam-` design system and CSS variables; do not introduce a second
  visual language.
- Use Playwright MCP for desktop and mobile screenshots and attach the
  evidence. Static checks do not prove a visual match.
- Livewire components dispatch commands and queries; business logic stays in
  `app/src`.

This rule is a pointer, not a duplicate of the skills.
