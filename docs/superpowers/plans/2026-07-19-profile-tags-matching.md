# Profile Tags Matching Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build controlled profile tags with visibility, profile display, discovery shared-interest hints, and tag-based soft ranking.

**Architecture:** Add Profile-owned taxonomy tables and models, keep save behavior in the existing Profile CQRS command, and keep discovery ranking in the Discovery domain service. Tags are controlled seed data; users only select existing tags and per-tag visibility.

**Tech Stack:** Laravel 12, PHP 8.4, Blade, MariaDB, PHPUnit, PHPStan, PHP-CS-Fixer.

---

## File Structure

- Create `app/database/migrations/2026_07_19_000012_create_profile_tag_tables.php` for categories, tags, and profile pivot.
- Create `app/app/Models/ProfileTagCategory.php` and `app/app/Models/ProfileTag.php`.
- Modify `app/app/Models/DatingProfile.php` with tag relationship and helper methods.
- Create `app/database/seeders/ProfileTagTaxonomySeeder.php` and call it from `DatabaseSeeder`.
- Modify `DemoDatingProfileSeeder` to assign realistic tag sets.
- Modify `SaveDatingProfileCommand` and `SaveDatingProfileOnboardingService` to save tag selections.
- Modify `profile/onboarding/show.blade.php`, `profile/show.blade.php`, and `shared/discovery/profile-card.blade.php` to display tags.
- Modify `FindNextCandidateService` to score and sort candidates by shared tags.
- Add/modify feature tests under `app/tests/Feature/Profile` and `app/tests/Feature/Discovery`.

## Tasks

### Task 1: Schema, Models, and Taxonomy Seeder

**Files:**
- Create: `app/database/migrations/2026_07_19_000012_create_profile_tag_tables.php`
- Create: `app/app/Models/ProfileTagCategory.php`
- Create: `app/app/Models/ProfileTag.php`
- Modify: `app/app/Models/DatingProfile.php`
- Create: `app/database/seeders/ProfileTagTaxonomySeeder.php`
- Modify: `app/database/seeders/DatabaseSeeder.php`
- Test: `app/tests/Feature/Profile/ProfileTagTaxonomySeederTest.php`

- [ ] Write a failing test asserting the taxonomy seeder creates categories and tags idempotently.
- [ ] Run `docker compose exec php ./vendor/bin/phpunit tests/Feature/Profile/ProfileTagTaxonomySeederTest.php`; expect missing class/table failure.
- [ ] Add migration with unique category/tag slugs and `dating_profile_tags.visibility`.
- [ ] Add Eloquent models and `DatingProfile::tags()`.
- [ ] Add idempotent taxonomy seeder from the spec categories.
- [ ] Run the targeted test until it passes.
- [ ] Commit with `feat: add profile tag taxonomy`.

### Task 2: Profile Tag Editing

**Files:**
- Modify: `app/src/Profile/RequestHandler/SaveDatingProfile/SaveDatingProfileCommand.php`
- Modify: `app/src/Profile/DomainService/SaveDatingProfileOnboardingService.php`
- Modify: `app/resources/views/profile/onboarding/show.blade.php`
- Test: `app/tests/Feature/Profile/ProfileTagSelectionTest.php`

- [ ] Write failing tests for saving selected tags with `public`, `matches_only`, and `private_matching` visibility.
- [ ] Run the targeted test; expect missing UI/save behavior.
- [ ] Extend the command payload with `profile_tags` array validation.
- [ ] Sync selected tags in the domain service after profile save.
- [ ] Render tag checkboxes grouped by category with a visibility select.
- [ ] Run the targeted test until it passes.
- [ ] Commit with `feat: add profile tag editing`.

### Task 3: Profile and Discovery Display

**Files:**
- Modify: `app/resources/views/profile/show.blade.php`
- Modify: `app/resources/views/shared/discovery/profile-card.blade.php`
- Test: `app/tests/Feature/Chat/ConversationTest.php`
- Test: `app/tests/Feature/Discovery/DiscoveryCardTest.php`

- [ ] Write failing tests that matched profile pages show bio plus public and matches-only tags.
- [ ] Write failing tests that discovery cards show shared public tags only.
- [ ] Add grouped tag display to matched profile page.
- [ ] Add shared-interest line to discovery profile card.
- [ ] Run targeted tests until they pass.
- [ ] Commit with `feat: show profile tags`.

### Task 4: Discovery Soft Ranking

**Files:**
- Modify: `app/src/Discovery/DomainService/FindNextCandidateService.php`
- Test: `app/tests/Feature/Discovery/DiscoveryCardTest.php`

- [ ] Write a failing test where two eligible candidates exist and the one with more shared tags appears first.
- [ ] Update candidate loading to eager-load tags.
- [ ] Score candidates by intersecting viewer/candidate tag IDs across all visibility levels.
- [ ] Sort by score descending, then profile ID ascending.
- [ ] Run targeted tests until they pass.
- [ ] Commit with `feat: rank discovery by shared tags`.

### Task 5: Final Verification

**Files:**
- Modify docs only if implementation differs from `docs/freedateapp-profile-tags-and-matching-spec.md`.

- [ ] Run `docker compose exec php composer test`; expect all tests pass.
- [ ] Run `docker compose exec php composer phpstan`; expect no errors.
- [ ] Run `docker compose exec php composer cs-check`; expect no fixable files.
- [ ] Run `docker compose exec php php artisan optimize:clear`.
- [ ] Run migrations and seeders locally if needed.
- [ ] Commit any final documentation or cleanup.

## Self-Review

The plan covers taxonomy, editing, profile display, discovery display, ranking,
demo seed data, tests, and verification from the specification. No hard tag
filters are included because the spec defines them as a future extension.
