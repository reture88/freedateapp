# Metrics Domain Context

## Purpose

Owns product metric events and the admin metrics dashboard. It records
event rows and aggregates them for operational visibility.

It does not own the product decisions that the metrics describe.

## Core Responsibilities

- record a metric event with an optional user and payload
- aggregate known events for the admin dashboard
- keep event names stable and documented

## Main Entry Points

### Presentation

- `app/src/Metrics/Presentation/Routes/routes.php`
- `AdminMetricsDashboardController`
- View: `app/resources/views/admin/metrics/index.blade.php`

### Domain Services

- `MetricRecorderService`
- `MetricsDashboardService`

## Important Services

### `MetricRecorderService`

- Input: event name, optional user id, payload array.
- Creates a `MetricEvent` with `occurred_at = now()`.
- Returns the event.

### `MetricsDashboardService`

- Groups `MetricEvent` by `event_name`.
- Returns a fixed map of dashboard labels to counts.

## Business Rules

- Dashboard keys are:
  - `registration_completed`
  - `profile_completed`
  - `active_user`
  - `match_created`
  - `first_message_sent`
  - `report_submitted`
  - `block_created`
  - `unmatch_created`
  - `assistant_starter_posted`
- Unknown/missing events render as `0`.
- The dashboard is admin-only.

## Dependencies

Internal:

- `App\Models\MetricEvent`

External:

- None.

## Data / Ownership

Owns:

- metric event rows
- dashboard aggregates

Does not own:

- domain events; those are recorded by the domain that owns the behavior.

## Permissions / Capability Model

- `auth` + `admin` for the dashboard route.
- Recording is a service call; no public route.

## Known Constraints

- `MetricRecorderService` is available but not wired broadly yet; many events
  are not recorded automatically.
- The dashboard reads `MetricEvent` aggregates, not a separate warehouse.
- Payload is JSON and may be empty.

## Gotchas

- Do not rename an event without updating the dashboard map and any tests.
- Do not insert `MetricEvent` rows directly from controllers; use the service.
- The dashboard is a read model; do not trigger writes from it.
- The event names in the dashboard map are not all emitted today; treating a
  zero as "not instrumented" is usually correct.

## Related Files

- `app/src/Metrics/DomainService/MetricRecorderService.php`
- `app/src/Metrics/DomainService/MetricsDashboardService.php`
- `app/src/Metrics/Presentation/Controller/AdminMetricsDashboardController.php`
- `app/app/Models/MetricEvent.php`
- `app/resources/views/admin/metrics/index.blade.php`
- `docs/architektura.md`

## When To Update This File

Update when:

- an event name changes
- a new dashboard metric is added
- recording is wired into a new domain
- the dashboard access rule changes
