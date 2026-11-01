# Design Review: Title

```text
Task ID: <issue>-design-<n>
Issue: #<issue> <url>
Author: frontend-engineer
Date: <YYYY-MM-DD>
Status: pending | in_progress | blocked | awaiting_approval | completed | failed | rejected
Verdict: pass | pass with gaps | fail
```

## Design source

- Figma frame: <name, node, or URL>
- Design brief: `docs/freedateapp-figma-design-brief.md` <section>
- Approved deviation: <none or approval reference>

## Screens and states

| Screen / state | Figma frame | Screenshot (desktop) | Screenshot (mobile) |
| --- | --- | --- | --- |
| Default | <frame> | <path> | <path> |
| Empty | <frame> | <path> | <path> |
| Validation error | <frame> | <path> | <path> |
| Loading / disabled | <frame> | <path> | <path> |
| Success | <frame> | <path> | <path> |
| Long content / overflow | <frame> | <path> | <path> |

## Comparison

| Aspect | Result | Notes |
| --- | --- | --- |
| Layout and hierarchy | pass / fail | <note> |
| Spacing and alignment | pass / fail | <note> |
| Type scale and weight | pass / fail | <note> |
| Colors and borders | pass / fail | <note> |
| Component states | pass / fail | <note> |
| Responsive behavior | pass / fail | <note> |
| Overflow and clipping | pass / fail | <note> |
| Accessibility basics | pass / fail | focus, labels, contrast, non-color cues |

## Implementation mapping

- View: `<path>`
- Livewire component: `<path>`
- Filament resource: `<path>` (if applicable)
- CSS: `app/public/css/app.css` <classes>

## Discrepancies

| Severity | Element | Figma | Running app | Action |
| --- | --- | --- | --- | --- |
| major / minor | <element> | <expected> | <actual> | <fix> |

## Remaining gaps

- <gap and reason, or "none">

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

<Pass with screenshots, or fail with the discrepancies required before QA.>
