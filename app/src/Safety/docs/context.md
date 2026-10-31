# Safety Domain Context

## Purpose

Owns reports, blocks, and admin report moderation. Safety data removes bad
actors from discovery/chat and records moderation decisions.

It does not own account status changes; `User` owns suspend/ban/deactivate/
restore.

## Core Responsibilities

- submit a report against a profile or other target
- block a user and update the connection
- let admins review and resolve reports
- hide a reported profile when the admin chooses `hide_profile`
- write an audit log for moderation actions
- broadcast new reports to admins

## Main Entry Points

### Presentation

- `app/src/Safety/Presentation/Routes/routes.php`
- `SubmitReportController`, `BlockUserController`
- `app/src/Safety/Presentation/Controller/Admin/`
- Views: `app/resources/views/admin/reports/`, `app/resources/views/safety/`

### Request Handlers

- `app/src/Safety/RequestHandler/SubmitReport/`
- `app/src/Safety/RequestHandler/BlockUser/`
- `app/src/Safety/RequestHandler/AdminModerateReport/`

### Domain Services

- `SubmitReportService`
- `BlockUserService`
- `AdminModerateReportService`

## Important Services

### `SubmitReportService`

- Creates a `Report` with `status = open`.
- Dispatches `ReportSubmitted`.

### `BlockUserService`

- Rejects self-block.
- Upserts a `Block` for the pair.
- Marks the matching connection `blocked` and sets `blocked_at`.

### `AdminModerateReportService`

- Resolves the report with reviewer, resolution, and `resolved_at`.
- On `hide_profile`, sets the target profile `visibility_status = hidden` and
  `moderation_status = rejected`.
- Writes an `AuditLog` entry with before/after data.

## Business Rules

- Reports start in `open`; admins resolve them.
- Report targets have a `target_type` and `target_id`.
- A block is directional in storage but excludes the pair in discovery (both
  directions are checked).
- A block on a matched pair sets the connection status to `blocked`.
- Admin actions are recorded in `AuditLog`.
- `hide_profile` only affects a profile target.

## Dependencies

Internal:

- `App\Models\Report`
- `App\Models\Block`
- `App\Models\AuditLog`
- `App\Models\Connection`
- `App\Models\DatingProfile`
- `App\Models\User`
- `App\Events\ReportSubmitted`

External:

- Laravel Reverb for the admin report broadcast.

## Data / Ownership

Owns:

- reports
- blocks
- moderation audit for report actions

Does not own:

- account status (`User`)
- discovery decisions (`Discovery`)
- profile content (`Profile`)

## Permissions / Capability Model

- `auth` + `account.active` + `verified` for report/block.
- `auth` + `admin` for the admin report queue and actions.

## Known Constraints

- There is no user-facing appeal/support flow yet.
- The report details are visible only in the admin area.
- Blocking is idempotent via `updateOrCreate`.
- Admin report actions are limited to the implemented action set.

## Gotchas

- Sort the profile pair before updating the connection.
- Do not change `account_status` in `AdminModerateReportService`; use
  `AdminModerateUserService`.
- Always write the audit entry for an admin action.
- `ReportSubmitted` is broadcast to admins; keep the channel consistent.
- A blocked connection must not be treated as matched by `Connection`/`Chat`.

## Related Files

- `app/src/Safety/Presentation/Routes/routes.php`
- `app/src/Safety/DomainService/SubmitReportService.php`
- `app/src/Safety/DomainService/BlockUserService.php`
- `app/src/Safety/DomainService/AdminModerateReportService.php`
- `app/app/Models/Report.php`
- `app/app/Models/Block.php`
- `app/app/Models/AuditLog.php`
- `docs/architektura.md`

## When To Update This File

Update when:

- report categories or targets change
- block behavior changes
- admin actions change
- audit fields change
- an appeal/support flow is added
