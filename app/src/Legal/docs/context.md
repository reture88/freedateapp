# Legal Domain Context

## Purpose

Owns the public terms-of-service and privacy-policy pages. It is a thin
presentation domain: routes and controllers render Blade views.

## Core Responsibilities

- expose `/terms-of-service` and `/privacy-policy`
- render the legal copy
- keep the pages reachable without authentication

## Main Entry Points

### Presentation

- `app/src/Legal/Presentation/Routes/routes.php`
- `app/src/Legal/Presentation/Controller/ShowTermsController.php`
- `app/src/Legal/Presentation/Controller/ShowPrivacyController.php`
- Views: `app/resources/views/legal/terms.blade.php`,
  `app/resources/views/legal/privacy.blade.php`

## Business Rules

- Both pages are public.
- The pages are linked from registration and the public layout.
- The copy is placeholder and visibly marked "Requires legal review before
  production use".

## Dependencies

Internal:

- `app/resources/views/layouts/public.blade.php`

External:

- None.

## Data / Ownership

Owns:

- public legal page rendering

Does not own:

- consent records
- account deletion policy
- privacy settings

## Permissions / Capability Model

- Public; no middleware.

## Known Constraints

- The legal copy has not been reviewed by a lawyer.
- There is no consent-acceptance tracking yet.
- The pages are static Blade views.

## Gotchas

- Do not remove the "requires legal review" note until the copy is approved.
- Keep the route names; registration links to them.
- The privacy page and the product privacy rules are not the same thing; the
  behavioral rules live in the domain code and technical spec.

## Related Files

- `app/src/Legal/Presentation/Routes/routes.php`
- `app/resources/views/legal/terms.blade.php`
- `app/resources/views/legal/privacy.blade.php`
- `docs/freedateapp-product-scope.md`
- `docs/architektura.md`

## When To Update This File

Update when:

- a legal page or route changes
- legal copy is approved or replaced
- consent tracking is introduced
- a new legal document is added
