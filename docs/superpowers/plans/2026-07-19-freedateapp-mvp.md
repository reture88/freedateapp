# Freedateapp MVP Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build the Freedateapp MVP on the existing Laravel/CQRS skeleton: completed dating profiles, preferences, one-at-a-time discovery, mutual matches, chat, AI Szárnysegéd starter messages, reporting/blocking, account controls, and admin moderation.

**Architecture:** Keep the existing Laravel 12 app and DDD/CQRS module pattern. Add focused domain modules under `app/src/` with single-action controllers, thin request handlers, domain services, Eloquent models, migrations, Livewire user-facing screens, Filament admin screens, Reverb realtime events, and feature/unit tests. Avoid adding a frontend SPA; prefer Laravel-native interactivity.

**Tech Stack:** Laravel 12, PHP 8.4, MariaDB, Redis, Livewire, Filament, Laravel Reverb, Blade layouts, PHPUnit, Larastan/PHPStan, PHP-CS-Fixer, Docker Compose.

---

## Source Documents

- Product scope: `docs/freedateapp-product-scope.md`
- Technical spec: `docs/freedateapp-technical-spec.md`
- Design brief: `docs/freedateapp-figma-design-brief.md`
- Repo guide: `AGENTS.md`
- Laravel skill references:
  - `.claude/skills/laravel-cqrs-conventions/SKILL.md`
  - `.claude/skills/laravel-definition-of-done/SKILL.md`

## Implementation Strategy

Build in vertical slices. Each epic must leave the app runnable and tested.

Order:

1. Data foundation and shared enums.
2. Profile onboarding.
3. Preferences.
4. Discovery and decisions.
5. Connections and matched-only chat.
6. AI Szárnysegéd.
7. Safety, reports, blocks, account controls.
8. Admin moderation and metrics.
9. Design-readiness polish and final review.
10. Livewire, Filament, and Reverb platform migration.

Every story follows TDD where practical:

1. Write failing feature or unit test.
2. Implement minimum behavior.
3. Run targeted test.
4. Run broader checks at epic end.
5. Commit.

## File Structure Decisions

Create these modules:

- `app/src/Profile/`
- `app/src/Preference/`
- `app/src/Discovery/`
- `app/src/Connection/`
- `app/src/Chat/`
- `app/src/Safety/`
- `app/src/Assistant/`
- `app/src/Metrics/`

Create Eloquent models in `app/app/Models/` when persistence is needed:

- `DatingProfile`
- `ProfilePhoto`
- `ProfilePreference`
- `DiscoveryDecision`
- `Connection`
- `Conversation`
- `Message`
- `Report`
- `Block`
- `AuditLog`
- `MetricEvent`

Blade layouts and partials live under `app/resources/views/`. New user-facing
workflow screens should be implemented as Livewire components under
`app/app/Livewire/` with matching views under `app/resources/views/livewire/`.
Admin and moderation workflows should use Filament resources and pages under
`app/app/Filament/`.

Tests live under:

- `app/tests/Feature/Profile/`
- `app/tests/Feature/Preference/`
- `app/tests/Feature/Discovery/`
- `app/tests/Feature/Connection/`
- `app/tests/Feature/Chat/`
- `app/tests/Feature/Safety/`
- `app/tests/Feature/Admin/`
- `app/tests/Unit/<Module>/`

## Epic 1: Data Foundation

**Goal:** Add database tables, Eloquent models, constants/enums, and factories needed by all later epics.

### Story 1.1: Dating Profile Persistence

**Files:**

- Create migration: `app/database/migrations/YYYY_MM_DD_000001_create_dating_profiles_table.php`
- Create model: `app/app/Models/DatingProfile.php`
- Modify: `app/app/Models/User.php`
- Test: `app/tests/Unit/Profile/DatingProfileModelTest.php`

Acceptance:

- User has one dating profile.
- Profile stores public dating fields separately from `users`.
- Profile has `hidden`, `visible`, `incomplete`, `suspended`, `banned` statuses.

Steps:

- [x] Write model relationship test for `User::datingProfile()`.
- [x] Add migration with user foreign key, display name, birthdate, gender, orientation, city, country code, approximate lat/lng, relationship intent, bio, visibility status, moderation status, AI wingman flag, completed timestamp.
- [x] Add `DatingProfile` model with fillable fields and casts.
- [x] Add `datingProfile()` relation to `User`.
- [x] Run `docker compose exec php vendor/bin/phpunit --filter DatingProfileModelTest`.
- [x] Commit: `feat: add dating profile persistence`.

### Story 1.2: Preferences, Photos, Decisions, Connections, Chat Tables

**Files:**

- Create migrations for profile photos, preferences, decisions, connections, conversations, messages.
- Create Eloquent models listed in File Structure Decisions.
- Test: `app/tests/Unit/Connection/ConnectionModelTest.php`

Acceptance:

- Tables support one profile preference row per profile.
- Discovery decisions are unique per viewer-target pair.
- Connections use deterministic profile ordering.
- Conversations belong to matched connections.
- Messages support user and assistant sender types.

Steps:

- [x] Write tests for model relationships and unique constraints.
- [x] Create migrations with explicit foreign keys and indexes.
- [x] Add Eloquent models and relationships.
- [x] Add deterministic connection helper method or domain value object.
- [x] Run targeted unit tests.
- [x] Commit: `feat: add dating interaction tables`.

### Story 1.3: Safety and Metrics Tables

**Files:**

- Create migrations for reports, blocks, audit logs, metric events.
- Create models: `Report`, `Block`, `AuditLog`, `MetricEvent`.
- Test: `app/tests/Unit/Safety/SafetyModelTest.php`

Acceptance:

- Reports can target profile, message, assistant message, or user.
- Blocks prevent future interactions.
- Audit logs store admin action metadata.
- Metric events store event name and JSON payload.

Steps:

- [x] Write model relationship tests.
- [x] Create migrations and indexes.
- [x] Add models and casts.
- [x] Run targeted unit tests.
- [x] Commit: `feat: add safety and metrics persistence`.

### Epic 1 Review Gate

- [x] `docker compose exec php composer test`
- [x] `docker compose exec php composer phpstan`
- [x] `docker compose exec php composer cs-check`
- [x] Confirm no route behavior changed yet.
- [x] Review migrations for reversible down methods and matching foreign key types.

## Epic 2: Profile Onboarding

**Goal:** Let users create, edit, preview, hide, and complete a dating profile.

### Story 2.1: Profile Onboarding Wizard

**Files:**

- Create module: `app/src/Profile/`
- Create controllers for onboarding steps.
- Create views under `app/resources/views/profile/onboarding/`.
- Create routes: `app/src/Profile/Presentation/Routes/routes.php`
- Test: `app/tests/Feature/Profile/ProfileOnboardingTest.php`

Acceptance:

- Logged-in user without completed profile is sent to onboarding.
- User can save basics, photos placeholder metadata, bio/prompts, interests, intent, and privacy consent.
- Completed profile gets `completed_at`.
- Incomplete profile does not enter discovery.

Steps:

- [x] Write feature test for completing onboarding from basics to preview.
- [x] Add Profile module routes guarded by auth middleware.
- [x] Add command/request handlers and domain services for saving profile sections.
- [x] Add Blade/Livewire views using existing layout patterns.
- [x] Add redirect logic after login/registration for incomplete profiles.
- [x] Run `docker compose exec php vendor/bin/phpunit --filter ProfileOnboardingTest`.
- [x] Commit: `feat: add profile onboarding`.

### Story 2.2: Profile Edit and Visibility

**Files:**

- Create controllers and views for profile edit, preview, and visibility.
- Test: `app/tests/Feature/Profile/ProfileVisibilityTest.php`

Acceptance:

- User can edit profile after completion.
- User can hide/show profile from discovery.
- Hidden profile cannot appear in discovery.

Steps:

- [x] Write feature tests for edit and hide/show.
- [x] Add `UpdateProfileVisibilityService`.
- [x] Add edit/preview views.
- [x] Add UI links from authenticated layout.
- [x] Run targeted tests.
- [x] Commit: `feat: add profile editing and visibility controls`.

### Epic 2 Review Gate

- [x] Run full tests, PHPStan, and style check.
- [ ] Browser smoke: register, complete onboarding, preview profile, hide/show profile.
- [x] Confirm incomplete profile cannot reach discovery.

## Epic 3: Preferences

**Goal:** Store and edit matching preferences, including AI Szárnysegéd exclusion.

### Story 3.1: Preference Editing

**Files:**

- Create module: `app/src/Preference/`
- Create views: `app/resources/views/preference/edit.blade.php`
- Test: `app/tests/Feature/Preference/PreferenceEditTest.php`

Acceptance:

- User can set age range, distance, preferred genders/orientations, relationship intent, and AI Szárnysegéd exclusion.
- Invalid ranges are rejected.
- Defaults exist after profile completion.

Steps:

- [x] Write feature tests for valid and invalid preference saves.
- [x] Add Preference module routes and controller.
- [x] Add command, handler, service, and response classes.
- [x] Add edit form with clear labels and helper text.
- [x] Run targeted tests.
- [x] Commit: `feat: add dating preferences`.

### Story 3.2: Preference Summary for Design and Discovery

**Files:**

- Create query service for preference summary.
- Modify profile/settings navigation.
- Test: `app/tests/Unit/Preference/PreferenceSummaryTest.php`

Acceptance:

- Discovery can consume normalized hard filters.
- UI can display a compact preference summary.

Steps:

- [x] Write unit test for normalized hard filter output.
- [x] Add query and response classes.
- [x] Add summary component partial.
- [x] Run targeted tests.
- [x] Commit: `feat: expose preference summary`.

### Epic 3 Review Gate

- [x] Full tests, PHPStan, style check.
- [ ] Browser smoke: update preferences and confirm validation messages.
- [ ] Design check: labels match `docs/freedateapp-figma-design-brief.md`.

## Epic 4: Discovery

**Goal:** Show one eligible profile at a time and record accept/reject decisions.

### Story 4.1: Candidate Query

**Files:**

- Create module: `app/src/Discovery/`
- Create candidate query service.
- Test: `app/tests/Unit/Discovery/CandidateQueryTest.php`

Acceptance:

- Candidate query excludes current user, incomplete profiles, hidden profiles, moderated profiles, blocked users, previous decisions, and AI-enabled profiles when excluded.
- Candidate query applies age, distance, gender/orientation, and relationship intent filters.

Steps:

- [x] Write unit tests with seeded profiles for each exclusion rule.
- [x] Add `FindNextCandidateQuery`, handler, response, and domain service.
- [x] Add distance calculation utility using approximate coordinates.
- [x] Run targeted tests.
- [x] Commit: `feat: add discovery candidate query`.

### Story 4.2: Discovery Card UI

**Files:**

- Create controller and route for discovery screen.
- Create view: `app/resources/views/discovery/show.blade.php`
- Test: `app/tests/Feature/Discovery/DiscoveryCardTest.php`

Acceptance:

- User sees one candidate card.
- Empty state appears when no candidates exist.
- Card shows photo, display name, age, location, intent, bio, and shared interests.

Steps:

- [x] Write feature tests for candidate and empty state.
- [x] Add controller and route.
- [x] Add Blade/Livewire view and profile card partial.
- [x] Run targeted tests.
- [x] Commit: `feat: add discovery card screen`.

### Story 4.3: Accept and Reject Decisions

**Files:**

- Create accept/reject commands and handlers.
- Modify discovery view actions.
- Test: `app/tests/Feature/Discovery/DiscoveryDecisionTest.php`

Acceptance:

- Reject records a decision and advances the queue.
- Accept records a decision and creates/updates a connection.
- Reciprocal accept creates a match and conversation.

Steps:

- [x] Write feature tests for reject, one-sided accept, and mutual accept.
- [x] Add decision command classes and services.
- [x] Add forms/buttons for accept/reject.
- [x] Run targeted tests.
- [x] Commit: `feat: add discovery decisions and matching`.

### Epic 4 Review Gate

- [x] Full tests, PHPStan, style check.
- [ ] Browser smoke: create two users, complete profiles, accept from both sides, confirm match.
- [x] Confirm rejected profiles do not reappear.

## Epic 5: Connections and Chat

**Goal:** Let matched users see connections and exchange text messages.

### Story 5.1: Match List

**Files:**

- Create module: `app/src/Connection/`
- Create view: `app/resources/views/connection/index.blade.php`
- Test: `app/tests/Feature/Connection/MatchListTest.php`

Acceptance:

- User sees only matched connections.
- Pending, unmatched, blocked, and moderated connections are excluded.

Steps:

- [x] Write feature tests for match list filtering.
- [x] Add query and controller.
- [x] Add match list view.
- [x] Run targeted tests.
- [x] Commit: `feat: add match list`.

### Story 5.2: Conversation View and Send Message

**Files:**

- Create module: `app/src/Chat/`
- Create view: `app/resources/views/chat/show.blade.php`
- Test: `app/tests/Feature/Chat/ConversationTest.php`

Acceptance:

- Only matched users can open conversation.
- User can send text message.
- Empty conversation state is shown before first message.
- Message composer rejects empty or overlong text.

Steps:

- [x] Write feature tests for access control and sending.
- [x] Add chat routes, controller, command, handler, and service.
- [x] Add conversation view and message partials.
- [x] Run targeted tests.
- [x] Commit: `feat: add matched chat`.

### Story 5.3: Unmatch

**Files:**

- Add unmatch command/service.
- Add modal/action from match list and chat.
- Test: `app/tests/Feature/Connection/UnmatchTest.php`

Acceptance:

- Either user can unmatch.
- Unmatched connection disappears from active match list.
- Chat becomes inaccessible except for admin moderation metadata.

Steps:

- [x] Write feature tests for unmatch flow.
- [x] Add service and route.
- [x] Add confirmation modal.
- [x] Run targeted tests.
- [x] Commit: `feat: add unmatch flow`.

### Epic 5 Review Gate

- [x] Full tests, PHPStan, style check.
- [ ] Browser smoke: match two users, send messages, unmatch, confirm chat access denied.

## Epic 6: AI Szárnysegéd

**Goal:** Add opt-in assistant starter messages for empty matched conversations.

### Story 6.1: AI Szárnysegéd Settings

**Files:**

- Modify Profile and Preference settings views.
- Create Assistant module: `app/src/Assistant/`
- Test: `app/tests/Feature/Assistant/AiWingmanSettingsTest.php`

Acceptance:

- User can enable/disable AI Szárnysegéd on own profile.
- User can exclude AI-enabled profiles from discovery.
- Discovery respects the exclusion.

Steps:

- [x] Write feature tests for toggles and discovery exclusion.
- [x] Add profile setting command/service.
- [x] Wire preference exclusion into candidate query.
- [x] Run targeted tests.
- [x] Commit: `feat: add ai wingman settings`.

### Story 6.2: Assistant Starter Generator

**Files:**

- Create `AssistantStarterService`.
- Create template/value object classes under `app/src/Assistant/`.
- Test: `app/tests/Unit/Assistant/AssistantStarterServiceTest.php`

Acceptance:

- Service builds one safe humorous message from visible non-sensitive profile facts.
- Service never uses missing facts.
- Service refuses unsafe context.
- Service produces deterministic text in MVP.

Steps:

- [x] Write unit tests for normal, missing-fact, and unsafe contexts.
- [x] Add context DTO and template selection service.
- [x] Add safety guard rules.
- [x] Run targeted tests.
- [x] Commit: `feat: add ai wingman starter generation`.

### Story 6.3: Scheduled Assistant Posting

**Files:**

- Add Laravel command or queued job.
- Modify `app/app/Console/Kernel.php`.
- Test: `app/tests/Feature/Assistant/AiWingmanPostingTest.php`

Acceptance:

- Empty matched conversations receive one assistant message after waiting period.
- Conversations with user messages are skipped.
- Assistant never posts twice to same conversation.
- Assistant message is rendered as AI Szárnysegéd in chat.

Steps:

- [x] Write feature tests using time travel.
- [x] Add command/job and domain service.
- [x] Register schedule.
- [x] Add assistant message bubble partial.
- [x] Run targeted tests.
- [x] Commit: `feat: add ai wingman scheduled starter`.

### Epic 6 Review Gate

- [x] Full tests, PHPStan, style check.
- [ ] Manual smoke: create match, wait/trigger command, confirm assistant message appears once.
- [x] Review assistant copy against design brief and safety rules.

## Epic 7: Safety and Account Controls

**Goal:** Add report, block, hide, deactivate, and delete flows.

### Story 7.1: Reports

**Files:**

- Create module: `app/src/Safety/`
- Create views for report modal/confirmation.
- Test: `app/tests/Feature/Safety/ReportFlowTest.php`

Acceptance:

- User can report profile, message, or assistant message.
- Report stores category and optional note.
- User sees confirmation.

Steps:

- [x] Write feature tests for each report target type.
- [x] Add report command/service.
- [x] Add report routes and modal partials.
- [x] Run targeted tests.
- [x] Commit: `feat: add report flow`.

### Story 7.2: Blocks

**Files:**

- Add block command/service.
- Modify discovery, match list, chat authorization.
- Test: `app/tests/Feature/Safety/BlockFlowTest.php`

Acceptance:

- Blocking prevents discovery, matching, and messaging in both directions.
- Existing matched connection becomes blocked.
- Blocked user cannot access chat through direct URL.

Steps:

- [x] Write feature tests for block effects.
- [x] Add block service and route.
- [x] Update candidate query and chat authorization.
- [x] Run targeted tests.
- [x] Commit: `feat: add user blocking`.

### Story 7.3: Account Deactivation and Deletion

**Files:**

- Add account settings views and services.
- Test: `app/tests/Feature/User/AccountLifecycleTest.php`

Acceptance:

- User can hide profile from discovery.
- User can deactivate account.
- User can request deletion.
- Deactivated/deleted accounts are excluded from discovery and chat entry points.

Steps:

- [x] Write feature tests for account lifecycle.
- [x] Add settings routes and controllers.
- [x] Add services updating account/profile status.
- [x] Run targeted tests.
- [x] Commit: `feat: add account lifecycle controls`.

### Epic 7 Review Gate

- [x] Full tests, PHPStan, style check.
- [ ] Browser smoke report/block/deactivate flows.
- [x] Security review: blocked users cannot access direct URLs.

## Epic 8: Admin Moderation and Metrics

**Goal:** Give admins enough operational control for launch.

### Story 8.1: Report Queue

**Files:**

- Extend existing admin user module or create `app/src/Safety/Presentation/Controller/Admin/`.
- Create views under `app/resources/views/admin/reports/`.
- Test: `app/tests/Feature/Admin/ReportQueueTest.php`

Acceptance:

- Admin can list open reports.
- Admin can view report detail with safe context.
- Non-admin users cannot access report queue.

Steps:

- [x] Write feature tests for admin and non-admin access.
- [x] Add report queue query/controller/views.
- [x] Add admin navigation link.
- [x] Run targeted tests.
- [x] Commit: `feat: add admin report queue`.

### Story 8.2: Moderation Actions and Audit Log

**Files:**

- Add moderation command/services.
- Add audit log query/views.
- Test: `app/tests/Feature/Admin/ModerationActionTest.php`

Acceptance:

- Admin can resolve report, hide profile, suspend user, ban user, restore user.
- Every action records audit log.
- Report status updates include reviewer and resolution.

Steps:

- [x] Write feature tests for moderation actions and audit log.
- [x] Add services and routes.
- [x] Add confirmation forms.
- [x] Run targeted tests.
- [x] Commit: `feat: add moderation actions and audit logs`.

### Story 8.3: Metrics Dashboard

**Files:**

- Create `app/src/Metrics/`
- Create admin metrics view.
- Test: `app/tests/Feature/Admin/MetricsDashboardTest.php`

Acceptance:

- Dashboard shows registration, completed profile, active user, match, first message, report, block, unmatch, and assistant metrics.
- Metrics are aggregated from `MetricEvent`.

Steps:

- [x] Write feature test with seeded metric events.
- [x] Add metric recording service used by key domain services.
- [x] Add dashboard query/controller/view.
- [x] Run targeted tests.
- [x] Commit: `feat: add admin metrics dashboard`.

### Epic 8 Review Gate

- [x] Full tests, PHPStan, style check.
- [ ] Browser smoke admin report queue, moderation action, audit log, metrics.
- [x] Verify non-admin access denied for all admin routes.

## Epic 9: Design-Readiness and Launch Polish

**Goal:** Align UI with Figma brief and prepare product for the later design pass.

### Story 9.1: Shared UI Components

**Files:**

- Create Blade partials under `app/resources/views/components/`.
- Modify user-facing views to use shared partials.
- Test: existing feature tests plus snapshot-level assertions where practical.

Acceptance:

- Profile card, form errors, status badges, empty states, assistant bubble, and confirmation modals are reusable.
- UI copy matches design brief.

Steps:

- [x] Inventory duplicated UI fragments.
- [x] Extract profile card partial.
- [x] Extract status badge partial.
- [x] Extract confirmation modal partial.
- [x] Extract assistant message partial.
- [x] Run affected feature tests.
- [x] Commit: `refactor: extract dating ui components`.

### Story 9.2: Accessibility and Copy Pass

**Files:**

- Modify Blade views and CSS.
- Test: manual browser review plus existing feature tests.

Acceptance:

- Form labels are explicit.
- Destructive actions require confirmation.
- Focus states are visible.
- Empty states tell the user the next useful action.

Steps:

- [ ] Review all MVP screens against design brief.
- [ ] Fix missing labels and ambiguous buttons.
- [ ] Add helper text for sensitive and AI settings.
- [ ] Run full tests.
- [ ] Commit: `polish: improve dating app ux copy`.

### Story 9.3: Final Definition of Done

**Files:**

- Modify docs only if implementation decisions changed.
- Test: full suite and smoke checks.

Acceptance:

- Product scope, technical spec, and design brief match implemented behavior.
- All required checks pass.
- Launch risks are documented.

Steps:

- [ ] Run `docker compose exec php composer test`.
- [ ] Run `docker compose exec php composer phpstan`.
- [ ] Run `docker compose exec php composer cs-check`.
- [ ] Run `docker compose exec php php artisan route:list`.
- [ ] Browser smoke: onboarding, discovery, match, chat, assistant trigger, report, block, admin moderation.
- [ ] Update docs for any intentional behavior differences.
- [ ] Commit: `docs: align mvp implementation notes`.

## Final Review Checklist

- [ ] Registration and login work.
- [ ] Profile onboarding and editing work.
- [ ] Incomplete/hidden/moderated profiles do not appear in discovery.
- [ ] Preferences filter discovery correctly.
- [ ] AI Szárnysegéd exclusion filter works.
- [ ] Accept/reject decisions work.
- [ ] Mutual accept creates match and conversation.
- [ ] Chat is matched-only.
- [ ] AI Szárnysegéd posts once into eligible empty conversations.
- [ ] Report, block, unmatch, deactivate, and delete flows work.
- [ ] Admin report queue, moderation actions, audit logs, and metrics work.
- [ ] All MVP screens have enough structure for Figma refinement.
- [ ] No payment, boost, premium, video, or push-notification code is introduced.

## Epic 10: Livewire, Filament, and Reverb Platform Migration

**Goal:** Move the MVP from plain Blade workflow pages to Laravel-native
interactive surfaces while preserving the existing CQRS/domain boundaries.

### Story 10.1: Frontend Platform Packages and Layout Wiring

Acceptance:

- Livewire, Filament, and Reverb dependencies are installed.
- Base layouts load Livewire assets.
- Broadcasting/Reverb configuration exists and is documented.
- Existing routes and tests keep passing.

Steps:

- [x] Install Livewire, Filament, and Laravel Reverb through Composer.
- [x] Add Livewire scripts/styles to the authenticated layout.
- [x] Add Reverb environment defaults to `.env.example`.
- [x] Run tests and style checks.
- [x] Commit: `feat: add livewire filament reverb foundation`.

### Story 10.2: Convert User Workflows to Livewire

Acceptance:

- Profile editing, preferences, discovery, match list, and chat use Livewire
  components.
- Components dispatch commands/queries instead of owning domain behavior.
- Form validation remains in command/query DTOs or request-handler input
  objects, not in controllers.
- Chat keeps latest messages at the bottom and supports realtime append.

Steps:

- [x] Convert profile edit screens.
- [x] Convert preferences screens.
- [x] Convert discovery card and decision actions.
- [x] Convert match list and conversation screen.
- [x] Add Livewire feature tests for key interactions.
- [x] Commit per coherent workflow.

### Story 10.3: Build Filament Admin and Moderation

Acceptance:

- Admin users can review users, dating profiles, reports, audit logs, and
  metrics in Filament.
- Destructive moderation actions call domain services or command handlers.
- Tables support filtering by moderation status, report category, and date.

Steps:

- [x] Configure a Filament admin panel.
- [x] Add read-only resources for users, profiles, reports, audit logs, and
  metrics.
- [x] Add guarded moderation actions.
- [x] Add feature tests or policy tests for authorization.
- [x] Commit: `feat: add filament moderation panel`.

### Story 10.4: Reverb Realtime UX

Acceptance:

- New matches, incoming messages, read state, assistant starter messages, and
  moderation reports broadcast over Reverb.
- HTTP fallbacks remain functional when WebSockets are unavailable.
- Frontend shows reconnect/sending/error states where relevant.

Steps:

- [x] Define the `MessageSent` broadcast event and private conversation
  channel.
- [x] Wire the Livewire conversation panel listener for incoming messages.
- [x] Define the `MatchCreated` event and private user channel.
- [x] Reuse `MessageSent` for assistant starter messages.
- [x] Define the `ReportSubmitted` event and admin reports channel.
- [x] Define read-state events.
- [x] Wire Livewire listeners for match updates.
- [x] Broadcast assistant starter and report events.
- [x] Add integration tests for channel authorization.
- [x] Commit: `feat: add realtime dating events`.

## Execution Recommendation

Use subagent-driven development. Dispatch one subagent per story or small story
cluster, then review after each epic. Keep commits story-sized. Do not start the
next epic until the current epic review gate passes.
