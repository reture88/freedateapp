# Discovery Domain Context

## Purpose

Owns the one-card discovery experience: pick the next eligible candidate,
record the viewer's accept/reject decision, and create a matched connection
when both sides accept.

It does not own the match list, chat, or account state.

## Core Responsibilities

- find the next eligible candidate for a viewer profile
- apply hard filters (status, visibility, moderation, age, gender,
  orientation, intent, blocks, prior decisions, AI exclusion)
- apply age and distance preferences
- rank remaining candidates by shared tags
- record accept/reject decisions
- create or update a connection on accept
- transition to matched and create a conversation on reciprocal accept
- broadcast `MatchCreated`

## Main Entry Points

### Presentation

- `app/src/Discovery/Presentation/Routes/routes.php`
- `ShowDiscoveryController`, `AcceptDiscoveryController`,
  `RejectDiscoveryController`

### Request Handlers

- `app/src/Discovery/RequestHandler/FindNextCandidate/`
- `app/src/Discovery/RequestHandler/RecordDiscoveryDecision/`

### Domain Services

- `FindNextCandidateService`
- `RecordDiscoveryDecisionService`

### DTO

- `app/src/Discovery/Dto/DistanceCalculator.php`

### Livewire

- `app/app/Livewire/Discovery/DiscoveryCard.php`

## Important Services

### `FindNextCandidateService`

- Requires a preference for the viewer profile.
- Excludes previously decided profiles and blocked users (both directions).
- Requires the candidate's user to be `ACTIVE` and verified.
- Requires a completed, visible, approved, adult profile.
- Applies preferred genders, orientations, and relationship intents.
- Applies the AI-wingman exclusion preference.
- Applies age range and distance in PHP.
- Ranks by shared tag count descending, then profile id ascending.
- Returns `null` when no candidate remains.

### `RecordDiscoveryDecisionService`

- Upserts the viewer's decision for the target profile.
- On accept, creates/updates the connection with the profile pair sorted.
- A reciprocal accept moves the connection to `matched`, creates the
  conversation, and dispatches `MatchCreated` on the transition.
- Rejects a self-decision.

## Business Rules

- Discovery is one candidate at a time.
- A rejected or accepted profile is excluded from future discovery for that
  viewer.
- A block in either direction excludes the profile.
- A candidate must be 18+; the query uses the latest eligible birthdate.
- Distance is approximate and computed in PHP from latitude/longitude.
- A match requires both `profile_one_decision` and `profile_two_decision` to
  be `accepted`.

## Dependencies

Internal:

- `App\Models\DatingProfile`
- `App\Models\ProfilePreference`
- `App\Models\DiscoveryDecision`
- `App\Models\Connection`
- `App\Models\Conversation`
- `App\Models\Block`
- `App\Events\MatchCreated`
- `App\Profile\Support\AdultEligibility`
- `App\User\Enum\AccountStatus`

External:

- None.

## Data / Ownership

Owns:

- discovery decisions
- candidate selection and ranking
- match transition on reciprocal accept

Does not own:

- preference editing (`Preference`)
- match list rendering (`Connection`)
- chat (`Chat`)

## Permissions / Capability Model

- `auth` + `account.active` + `verified` + `dating.profile`.
- The viewer must have a dating profile and a preference.

## Known Constraints

- Distance is computed in PHP; there is no geospatial index yet.
- The candidate query loads candidates and filters in PHP for age/distance.
- The profile pair must be sorted before connection lookup/creation.
- One candidate at a time; no pagination.

## Gotchas

- Do not create a connection without sorting the two profile ids.
- Dispatch `MatchCreated` only on the transition to `matched`, not on every
  accept.
- Use `updateOrCreate` for decisions; a repeated decision must not duplicate.
- The conversation is created with `firstOrCreate` on a match.
- A missing preference returns no candidate; ensure defaults exist.
- Keep the adult filter and the account-status/verified filter aligned with
  the technical spec.

## Related Files

- `app/src/Discovery/Presentation/Routes/routes.php`
- `app/src/Discovery/DomainService/FindNextCandidateService.php`
- `app/src/Discovery/DomainService/RecordDiscoveryDecisionService.php`
- `app/src/Discovery/Dto/DistanceCalculator.php`
- `app/app/Livewire/Discovery/DiscoveryCard.php`
- `app/app/Models/DiscoveryDecision.php`
- `app/app/Models/Connection.php`
- `docs/architektura.md`

## When To Update This File

Update when:

- eligibility or ranking rules change
- the decision or match transition changes
- distance/age filtering changes
- the AI-wingman exclusion changes
- a new candidate source is introduced
