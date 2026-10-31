# Preference Domain Context

## Purpose

Owns dating preferences: age range, distance, genders, orientations,
relationship intents, AI-wingman exclusion, dealbreakers, and soft
preferences. Discovery reads these values.

It does not own discovery ranking or profile content.

## Core Responsibilities

- ensure a default preference exists for a profile
- save edited preferences
- build a human-readable preference summary
- expose the preference edit surface

## Main Entry Points

### Presentation

- `app/src/Preference/Presentation/Routes/routes.php`
- `EditPreferenceController`, `SavePreferenceController`

### Request Handlers

- `app/src/Preference/RequestHandler/GetPreferenceSummary/`
- `app/src/Preference/RequestHandler/SavePreference/`

### Domain Services

- `EnsureDefaultPreferenceService`
- `SavePreferenceService`
- `BuildPreferenceSummaryService`

### Livewire

- `app/app/Livewire/Preference/EditPreferenceForm.php`
- `app/app/Livewire/Preference/DiscoveryFilterSheet.php`

## Important Services

### `EnsureDefaultPreferenceService`

- Returns the existing preference or creates a default.
- Default: ages 18–80, 50 km, all genders, all orientations, all intents,
  AI-wingman exclusion off, empty dealbreakers/soft preferences.

### `SavePreferenceService`

- Loads the viewer's profile.
- Ensures a default exists.
- Updates the preference fields from the command.

### `BuildPreferenceSummaryService`

- Returns a `PreferenceSummary` with the stored values and a short English
  summary string.

## Business Rules

- A preference belongs to a `DatingProfile`.
- Discovery requires a preference; defaults are created on demand.
- `preferred_genders`, `preferred_orientations`, and
  `relationship_intents` are arrays/JSON.
- Minimum preferred age is 18 in the default.
- `exclude_ai_wingman_enabled` filters AI-enabled profiles in discovery.
- Dealbreakers and soft preferences are stored but their scoring behavior is
  intentionally limited in the MVP.

## Dependencies

Internal:

- `App\Models\ProfilePreference`
- `App\Models\DatingProfile`
- `App\Models\User`
- `App\Discovery\DomainService\FindNextCandidateService`

External:

- None.

## Data / Ownership

Owns:

- `ProfilePreference` values and defaults

Does not own:

- discovery filtering/ranking (`Discovery`)
- profile data (`Profile`)

## Permissions / Capability Model

- `auth` + `account.active` + `verified` + `dating.profile`.
- A user edits only their own profile's preference.

## Known Constraints

- Arrays are stored as JSON.
- The summary uses the first relationship intent for its human sentence.
- Dealbreakers/soft preferences have no full scoring engine yet.

## Gotchas

- Always create defaults through `EnsureDefaultPreferenceService`; do not
  duplicate the default values in a controller.
- Keep the default age minimum at 18.
- The discovery query expects all three preference arrays to exist.
- Do not move preference ownership to `User`; it is profile-scoped.

## Related Files

- `app/src/Preference/Presentation/Routes/routes.php`
- `app/src/Preference/DomainService/EnsureDefaultPreferenceService.php`
- `app/src/Preference/DomainService/SavePreferenceService.php`
- `app/src/Preference/DomainService/BuildPreferenceSummaryService.php`
- `app/app/Models/ProfilePreference.php`
- `app/app/Livewire/Preference/EditPreferenceForm.php`
- `docs/architektura.md`

## When To Update This File

Update when:

- a preference field is added or removed
- a default changes
- the discovery contract changes
- the summary format changes
- a scoring/dealbreaker engine is introduced
