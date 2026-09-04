# Freedateapp Technical Specification

## Purpose

This document translates `docs/freedateapp-product-scope.md` into a technical
specification for a Laravel 12 / PHP 8.4 MVP. It defines system boundaries,
domain modules, data concepts, workflows, UI surfaces, safety requirements, and
design handoff needs. It is not the step-by-step implementation plan; that lives
in `docs/superpowers/plans/2026-07-19-freedateapp-mvp.md`.

## Existing Baseline

The current app is a Dockerized Laravel backend with a hand-rolled CQRS/DDD
structure:

- Framework glue: `app/app/`
- Domain modules: `app/src/<Module>/`
- Routes: `app/src/<Module>/Presentation/Routes/routes.php`
- Tests: `app/tests/Feature` and `app/tests/Unit`
- Current modules: `Shared`, `Auth`, `User`

New functionality must follow the existing conventions:

- Controllers are single-action `__invoke()` classes.
- Request handlers are thin adapters around domain services.
- Domain services expose `__construct()` and `handle()`.
- Commands and queries self-construct from arrays and validate where useful.
- Module routes are auto-discovered by `RouteServiceProvider`.

## Frontend Platform Direction

Freedateapp should move to a Laravel-native interactive frontend stack:

- Livewire is the primary layer for user-facing app screens: onboarding,
  profile editing, preferences, discovery, matches, chat, and settings.
- Filament is the primary layer for admin and operational screens: moderation,
  reports, users, profiles, audit logs, and metrics.
- Laravel Reverb is the default WebSocket server for realtime UX where it adds
  clear value, especially chat message arrival, read state, match creation, and
  moderation queue updates.

The migration should be incremental. Existing Blade routes may stay in place
while individual screens are replaced by Livewire components. Domain behavior
still follows the CQRS convention: Livewire components dispatch commands and
queries rather than embedding business logic. Filament resources may use
Eloquent for simple read/list surfaces, but moderation actions and domain
mutations must call services or command handlers.

Current implementation status:

- Livewire, Filament, and Reverb packages are installed.
- Docker includes PHP `intl` and `pcntl` extensions required by Filament and
  Reverb.
- Docker Compose runs a dedicated `reverb` service exposed locally on port
  `8081`.
- Livewire powers dating profile onboarding/editing, profile photo upload and
  primary selection, preferences, discovery accept/reject, match list rendering,
  and the conversation panel.
- Chat messages are loaded oldest-to-newest by `created_at` and `id`, so the
  newest message appears at the bottom.
- Filament has a registered admin panel at `/admin-panel` with read-only list
  and detail resources for users, dating profiles, reports, audit logs, and
  metric events.
- Reverb is configured and running. Chat message sending broadcasts a private
  conversation event, and the Livewire conversation panel appends incoming
  message events. New matches broadcast to private user channels and the
  Livewire match list prepends incoming match events. Realtime read-state,
  assistant starter, and moderation report events are still planned.

## MVP Module Map

### Profile

Owns public dating profiles and onboarding completion.

Responsibilities:

- Create one dating profile per `User`.
- Store profile identity, bio, prompt answers, relationship intent, lifestyle,
  location summary, visibility status, AI Szárnysegéd opt-in, and completion
  state.
- Track profile moderation status separately from account status.
- Expose profile edit and onboarding screens.

### Preference

Owns search and discovery preferences.

Responsibilities:

- Store hard filters: age range, distance, gender/orientation compatibility,
  relationship intent, AI Szárnysegéd exclusion preference.
- Store soft preferences: interests, values, lifestyle, activity weighting.
- Expose edit screens and query APIs used by Discovery.

### Discovery

Owns the one-profile-at-a-time recommendation queue.

Responsibilities:

- Return the next eligible profile card for the current user.
- Apply hard filters, profile visibility, moderation status, previous decisions,
  blocks, and AI Szárnysegéd exclusion.
- Record accept/reject decisions.
- Create a pending connection when one side accepts.
- Promote to match when both sides accepted.

### Connection

Owns match lifecycle.

Responsibilities:

- Track `pending`, `matched`, `unmatched`, and `blocked` states.
- Enforce that chat only opens for matched connections.
- Provide unmatch flow.
- Provide metadata for admin moderation.

### Chat

Owns matched-only messaging.

Responsibilities:

- Create one conversation for each matched connection.
- Store text messages, author type, timestamps, read status, and hidden/dismissed
  state.
- Support report/block/unmatch entry points from conversation UI.
- Reserve system/assistant message support for AI Szárnysegéd.

### Safety

Owns reports, blocks, moderation states, and audit logging.

Responsibilities:

- Allow reporting profiles, messages, and AI assistant messages.
- Allow blocking users.
- Hide blocked users from discovery and chat entry points.
- Provide admin report queue and moderation actions.
- Record audit logs for admin decisions.

### Assistant

Owns AI Szárnysegéd behavior.

Responsibilities:

- Detect matched conversations that remain empty after a configured waiting
  period.
- Generate or select one safe humorous starter message.
- Insert the message as assistant/system persona, not as a user.
- Respect user opt-in and exclusion preferences.
- Ensure no sensitive, fabricated, sexual, insulting, discriminatory, or
  manipulative content appears.

Initial MVP can use deterministic templates plus profile facts. LLM generation
can be added later behind the same service interface.

### Metrics

Owns operational event logging.

Responsibilities:

- Record registration, profile completion, discovery decision, match, first
  message, report, block, unmatch, assistant trigger, and assistant dismissal
  events.
- Provide admin summary metrics.

## Core Data Concepts

### Users

Existing `App\Models\User` remains the authentication and account aggregate.
Dating-specific fields should not be added directly to `users` unless they are
account-level flags. Profile data belongs in dating profile tables.

Important account states:

- `active`
- `hidden`
- `suspended`
- `banned`
- `deleted`

### Dating Profiles

A dating profile belongs to one user.

Required MVP fields:

- `user_id`
- `display_name`
- `birthdate`
- `gender`
- `orientation`
- `city`
- `country_code`
- `approximate_latitude`
- `approximate_longitude`
- `relationship_intent`
- `bio`
- `visibility_status`
- `moderation_status`
- `ai_wingman_enabled`
- `completed_at`

Use approximate coordinates only. Never expose exact location.

### Profile Photos

Photos belong to a profile and include:

- file path
- sort order
- primary flag
- moderation status

MVP can use local Laravel storage. Future production storage can move to object
storage without changing domain behavior.

### Profile Attributes

Use controlled vocabularies for interests, values, lifestyle markers, and prompt
answers. Avoid free-form sensitive categories unless explicitly needed and
consented to.

### Preferences

Preferences belong to a profile and include:

- preferred age min/max
- preferred distance
- compatible genders/orientations
- relationship intent filter
- exclude AI Szárnysegéd-enabled profiles
- dealbreaker flags

### Discovery Decisions

One record per viewer/profile decision:

- `viewer_profile_id`
- `target_profile_id`
- `decision`: `accepted` or `rejected`
- `decided_at`

Uniqueness: one active decision per viewer-target pair.

### Connections

A connection represents interest state between two profiles.

Fields:

- `profile_one_id`
- `profile_two_id`
- `profile_one_decision`
- `profile_two_decision`
- `status`
- `matched_at`
- `unmatched_at`
- `blocked_at`

Store profile IDs in deterministic order to avoid duplicates.

### Conversations and Messages

A conversation belongs to one matched connection.

Message fields:

- `conversation_id`
- `sender_user_id` nullable for assistant/system messages
- `sender_type`: `user`, `assistant`, `system`
- `body`
- `read_at`
- `hidden_for_user_ids` or per-user message state table
- `created_at`

Assistant messages must be clearly distinguishable in UI and data.

### Reports, Blocks, and Audit Logs

Reports include reporter, target type, target ID, category, free-form note,
status, reviewer, resolution, and timestamps.

Blocks include blocker user, blocked user, reason category, and timestamps.

Audit logs include actor, action, target type, target ID, before/after summary,
and timestamp.

## Main Workflows

### Onboarding

1. User registers or logs in.
2. App detects no completed dating profile.
3. User completes profile wizard.
4. App validates minimum profile quality.
5. Completed profile becomes eligible for discovery if visible and not moderated.

### Discovery

1. User opens discovery.
2. Discovery query loads user profile and preferences.
3. Hard filters remove ineligible profiles.
4. Safety filters remove blocked, hidden, suspended, banned, deleted, and
   previously decided profiles.
5. Ranking orders remaining profiles by soft compatibility and recent activity.
6. UI presents one profile card.
7. User accepts or rejects.
8. Accept creates or updates a connection.
9. If reciprocal accept exists, connection becomes matched and conversation is
   created.

### Messaging

1. User opens match list.
2. User selects a matched connection.
3. Chat loads conversation messages.
4. User sends text message.
5. Message persists and becomes visible to both users unless blocked/unmatched.
6. Report, block, and unmatch remain available from the chat UI.

### AI Szárnysegéd

1. A scheduled job scans matched conversations with zero user messages.
2. It checks waiting period, both users' visibility/safety state, and whether at
   least one matched profile enabled AI Szárnysegéd.
3. It verifies neither user has excluded AI Szárnysegéd-enabled profiles in a way
   that should have prevented this match.
4. It builds a safe context from visible non-sensitive profile fields.
5. It creates one assistant message.
6. It records an assistant event for metrics and moderation.
7. It never posts a second assistant starter in the same conversation in MVP.

### Moderation

1. User reports a profile, message, or assistant message.
2. Report enters admin queue.
3. Admin reviews context.
4. Admin resolves report with no action, warning, hide profile, suspend, ban, or
   delete.
5. Audit log records the decision.

## UI Surface Inventory

Public/account:

- Login
- Registration
- Logout

Profile:

- Onboarding wizard
- Profile preview
- Profile edit
- Preferences edit
- Visibility and AI Szárnysegéd settings
- Account deactivate/delete

Discovery:

- One-card discovery screen
- Empty state when no profiles match filters
- Match success screen

Messaging:

- Match list
- Conversation screen
- AI Szárnysegéd assistant message display
- Report/block/unmatch modal

Admin:

- User list
- Profile detail
- Report queue
- Report review detail
- Audit log list
- Metrics dashboard

## Realtime Requirements

Use Reverb-backed broadcasting for events that materially improve flow:

- New matched conversation appears without a manual refresh.
- Incoming chat messages append at the bottom of the conversation.
- Read/delivered state updates without disturbing scroll position.
- AI Szárnysegéd starter messages appear as assistant/system messages.
- Admin report queue reflects newly submitted reports.

Realtime updates must be progressive enhancement. Core actions still complete
through normal HTTP requests so the app remains usable if WebSockets are
temporarily unavailable.

## Design Requirements for Later Figma Work

Figma artifacts should cover:

- Responsive mobile-first screens for onboarding, discovery, matching, chat, and
  profile settings.
- Desktop admin screens optimized for dense moderation workflows.
- Livewire component states and events for user-facing screens.
- Filament resource/page inventory for admin screens.
- Reverb event annotations for chat, match, read state, assistant starter, and
  moderation queue updates.
- Component library: buttons, icon buttons, form controls, tags, profile cards,
  photo uploader, chat bubbles, assistant message bubble, report modals, status
  badges, tabs, empty states, and admin tables.
- States: loading, empty, validation error, blocked, unmatched, suspended,
  hidden profile, no discovery candidates, assistant disabled, assistant
  triggered, assistant dismissed.
- Tone: warm, simple, direct, not gamified with casino-like urgency.
- Accessibility: readable contrast, keyboard-friendly controls, clear focus
  states, non-color-only status indicators.

## Security and Privacy Requirements

- Enforce authorization in every controller and domain service that reads or
  modifies profile, preference, connection, chat, report, or admin data.
- Never trust client-provided profile IDs without checking ownership and
  visibility.
- Use CSRF protection for forms.
- Validate file uploads by MIME, size, and image dimensions.
- Store only approximate location for matching.
- Keep sensitive profile attributes optional and consent-backed.
- Never expose report details to reported users.
- Ensure blocked users cannot discover, match, message, or view each other's
  profile through direct URLs.

## Review Gates

Every epic must finish with:

- PHPUnit feature tests for user-visible flows.
- PHPUnit unit tests for domain services where behavior is non-trivial.
- `docker compose exec php composer test`
- `docker compose exec php composer phpstan`
- `docker compose exec php composer cs-check`
- Manual smoke check in browser or curl for touched routes.
- Playwright visual smoke for changed Livewire screens when frontend behavior or
  layout changes.
- Short review note in the implementation PR or commit message summary.

## Open Product Defaults

These defaults are locked for MVP unless changed by a later product decision:

- Minimum age: 18.
- Discovery: one profile at a time.
- Chat: text only.
- AI Szárnysegéd: one starter message per empty matched conversation.
- Payments, boosts, subscriptions, video calls, and push notifications: out of
  MVP scope.
