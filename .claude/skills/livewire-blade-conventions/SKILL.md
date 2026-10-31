---
name: livewire-blade-conventions
description: 'Blade, Livewire 3, Filament 4 and plain-CSS conventions for this repo. Use when changing app/resources/views, app/app/Livewire, app/app/Filament, app/public/css/app.css, or any user-facing screen; also use when a task mentions a Figma frame, a Livewire component, an onboarding/profile/discovery/chat screen, or the admin panel.'
---

# Livewire / Blade / Filament Conventions

This repo uses a Laravel-native frontend: Blade + Livewire 3 + Filament 4 and
plain CSS. There is no Vue, Inertia, PrimeVue, SCSS, Node, or Vite build step.

## Where things live

- Livewire components: `app/app/Livewire/<Area>/`.
- Livewire views: `app/resources/views/livewire/<area>/`.
- Page and partial views: `app/resources/views/<area>/`.
- Reusable Blade partials: `app/resources/views/components/` and
  `app/resources/views/shared/`.
- Layouts: `app/resources/views/layouts/`.
- Admin views: `app/resources/views/admin/`.
- Filament resources: `app/app/Filament/`.
- CSS: `app/public/css/app.css`.

## Naming and structure

- Livewire class names are StudlyCase; views are kebab-case.
- New user-facing UI uses the `mtam-` design-system prefix (`mtam-auth-shell`,
  `mtam-landing`, `mtam-status-card`, ...).
- Use BEM-like class names for new components: `block__element--modifier`.
- Keep components small and focused. If a component owns more than one
  responsibility, split it.
- Use semantic HTML5 elements and label every form control.

## Livewire rules

- Livewire components dispatch Commands/Queries through the bus; do not put
  business logic in the component.
- Use `wire:model` for form state, server-side validation, and explicit
  loading/error states.
- Keep destructive actions behind a confirmation.
- Use `Livewire::test()` feature tests for behavior that matters.
- Realtime listeners use the existing private-channel event names
  (`MatchCreated`, `MessageSent`, `MessagesRead`, `ReportSubmitted`).

## Filament rules

- Filament is for admin and operational screens.
- Simple read-only lists may use Eloquent.
- Moderation and domain mutations call the existing service or command
  handler; do not reimplement business rules in a resource.
- Keep admin tables and infolists consistent with the existing account-status
  and moderation patterns.

## CSS rules

- Plain CSS in `app/public/css/app.css`; no preprocessor.
- Use CSS variables and the existing `mtam-` tokens for color, spacing, and
  type.
- Mobile-first: write the base rule for small screens, then add
  `@media (min-width: ...)`.
- Avoid inline styles; add a class instead.
- Ensure focus states, readable contrast, and non-color-only status cues.
- Do not introduce a second design system or a component framework.

## Visual verification

For any UI change, follow `figma-playwright-verification`:

1. Read the relevant Figma frame through Figma MCP.
2. Implement against that frame.
3. Use Playwright MCP to screenshot the running screen at desktop and mobile
   widths.
4. Compare and fix. Attach evidence to the pull request.

If the design is ambiguous, ask before inventing a new layout.

## Related

- `docs/architektura.md` — frontend boundaries.
- `docs/kod-konvencio.md` — short code convention.
- `docs/freedateapp-figma-design-brief.md` — design intent when no Figma
  frame is linked.
- `figma-playwright-verification` — visual verification flow.
