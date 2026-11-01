---
name: figma-playwright-verification
description: 'Use when implementing or reviewing any UI change: read the design from Figma MCP, then verify the running app with Playwright MCP screenshots. Use for Blade, Livewire, Filament, CSS, responsive, empty/error/loading state, or "does it match the design" tasks.'
---

# Figma + Playwright Verification

Figma is the design source of truth. Playwright is the visual verification
tool. The flow is MCP-based; the repo intentionally has no `package.json`,
Playwright config, or Node E2E suite.

## Before coding

1. Identify the screen and the relevant Figma frame. Use the Figma link in
   the GitHub Issue or pull request when present.
2. Read the frame through Figma MCP: layout, spacing, type scale, colors,
   component states, copy, and responsive variants.
3. If no Figma frame is linked, ask for it. If the user confirms no frame
   exists, use `docs/freedateapp-figma-design-brief.md` for intent and say so.
4. Map the frame to the implementation:
   - Blade/Livewire screen under `app/resources/views`.
   - Livewire component under `app/app/Livewire`.
   - CSS in `app/public/css/app.css` (`mtam-` prefix).
   - Filament admin screen when applicable.
5. Note gaps between the frame and the current implementation before editing.

## After implementing

1. Ensure the stack is running: `docker compose up -d`; the app is at
   `http://localhost:8080`.
2. Use Playwright MCP to open the screen and walk the touched flow.
3. Take screenshots at:
   - desktop width (for example 1440 px),
   - mobile width (for example 390 px).
4. Capture the relevant states:
   - default,
   - empty,
   - validation error,
   - loading or disabled,
   - success or confirmation,
   - long-content / overflow.
5. Compare each screenshot to the Figma frame:
   - layout and hierarchy,
   - spacing and alignment,
   - type scale and weights,
   - colors and borders,
   - component states,
   - responsive behavior,
   - overflow and clipping.
6. Fix discrepancies and repeat until aligned.
7. Attach the screenshots (or their paths) and a short "Figma vs running app"
   note to the pull request.

## Rules

- Do not claim a visual match without a screenshot or a manual browser check.
- Do not introduce a local Node/Playwright suite unless the user explicitly
  asks for one; use the MCP flow.
- If Figma MCP or Playwright MCP is unavailable, report it, fall back to a
  manual browser check, and mark the visual verification as partial.
- Keep the design-system classes (`mtam-`) and the existing CSS variables;
  do not add a second visual language.
- When the frame and the design brief disagree, ask which one wins.

## Evidence format for the pull request

```text
Figma frame: <name or URL>
Playwright: desktop + mobile screenshots
Compared: layout, spacing, type, color, states, responsive
Remaining gaps: <none or list>
```

## Related

- `livewire-blade-conventions` — implementation conventions.
- `docs/freedateapp-figma-design-brief.md` — design intent.
- `docs/freedateapp-technical-spec.md` — UI surface inventory and design
  requirements.
