# Profile Domain Context

## Purpose

Owns the public dating profile: onboarding, editing, photos, visibility,
preview, and the matched-profile view. It stores the data that discovery and
matches read.

It does not own account identity/status (`User`) or preferences
(`Preference`).

## Core Responsibilities

- create or update a user's dating profile
- enforce the 18+ rule
- manage profile photos and the primary photo
- manage profile visibility
- expose the profile preview and matched-profile view
- keep tags and defaults in sync

## Main Entry Points

### Presentation

- `app/src/Profile/Presentation/Routes/routes.php`
- `ShowOnboardingController`, `SaveOnboardingController`,
  `UploadProfilePhotoController`, `ChoosePrimaryProfilePhotoController`,
  `UpdateVisibilityController`, `PreviewProfileController`,
  `ShowMatchedProfileController`

### Request Handlers

- `app/src/Profile/RequestHandler/SaveDatingProfile/`
- `app/src/Profile/RequestHandler/UploadProfilePhoto/`
- `app/src/Profile/RequestHandler/ChoosePrimaryProfilePhoto/`
- `app/src/Profile/RequestHandler/UpdateProfileVisibility/`
- `app/src/Profile/RequestHandler/ShowMatchedProfile/`

### Domain Services

- `SaveDatingProfileOnboardingService`
- `UploadProfilePhotoService`
- `ChoosePrimaryProfilePhotoService`
- `UpdateDatingProfileVisibilityService`
- `ShowMatchedProfileService`
- `AdultEligibility` support

### Livewire

- `app/app/Livewire/Profile/DatingProfileForm.php`

## Important Services

### `SaveDatingProfileOnboardingService`

- Updates or creates the profile for the user.
- Stores identity, birthdate, gender, orientation, city/country, approximate
  coordinates, intent, bio, AI-wingman flag.
- Sets `visibility_status = visible`, `moderation_status = pending`,
  `completed_at = now()`.
- Ensures the default preference.
- Syncs tags with per-tag visibility and sort order.

### `UploadProfilePhotoService`

- Rejects more than 7 photos.
- Stores the file on the public disk.
- Makes the first photo primary.
- Sets `moderation_status = approved` for uploads.

### `ChoosePrimaryProfilePhotoService`

- Clears the previous primary and sets the chosen photo primary in a
  transaction.

### `UpdateDatingProfileVisibilityService`

- Updates `visibility_status` for the viewer's profile.

### `ShowMatchedProfileService`

- Allows the viewer's own profile if it is 18+.
- Otherwise requires a matched connection, an adult viewer, an adult target,
  an active + verified target user.

## Business Rules

- One dating profile per user.
- A profile is visible and `moderation_status = pending` after onboarding.
- A profile is only discoverable when completed, visible, approved, adult, and
  its user is active and verified.
- Maximum 7 photos; the first photo is primary.
- Visibility is `visible` or `hidden`.
- Deactivate/delete flows hide the profile.
- The 18+ rule uses `AdultEligibility`; the boundary is inclusive.

## Dependencies

Internal:

- `App\Models\DatingProfile`
- `App\Models\ProfilePhoto`
- `App\Models\ProfileTag`
- `App\Models\User`
- `App\Preference\DomainService\EnsureDefaultPreferenceService`
- `App\Profile\Support\AdultEligibility`
- `App\Models\Connection` (matched-profile access)

External:

- Laravel public filesystem disk for photos.

## Data / Ownership

Owns:

- dating profile content
- photos
- tags and tag visibility
- profile visibility and moderation status

Does not own:

- account status (`User`)
- preferences (`Preference`)
- discovery decisions (`Discovery`)

## Permissions / Capability Model

- `auth` + `account.active` + `verified` for onboarding/edit/visibility/
  preview/profile view.
- `dating.profile` for photo upload and primary selection.
- Matched-profile view requires a matched connection unless it is the
  viewer's own profile.

## Known Constraints

- Photo storage is the public disk.
- Uploads are marked approved; there is no automatic image moderation.
- The profile edit route reuses the onboarding controller/service.
- The 18+ check is duplicated at display time for safety.

## Gotchas

- `SaveOnboardingController` handles both create and edit; keep both routes
  in sync.
- Do not update `visibility_status` or `moderation_status` in an unrelated
  profile edit without a reason.
- Tags are synced with a pivot payload; do not replace with a plain
  `sync([$ids])`.
- The first uploaded photo becomes primary; later uploads do not steal it.
- A deactivated or deletion-requested user's profile is hidden by `User`, not
  by this domain; keep the interaction in mind.
- The adult boundary is inclusive: exactly 18 is eligible.

## Related Files

- `app/src/Profile/Presentation/Routes/routes.php`
- `app/src/Profile/DomainService/SaveDatingProfileOnboardingService.php`
- `app/src/Profile/DomainService/UploadProfilePhotoService.php`
- `app/src/Profile/DomainService/ShowMatchedProfileService.php`
- `app/src/Profile/Support/AdultEligibility.php`
- `app/app/Models/DatingProfile.php`
- `app/app/Livewire/Profile/DatingProfileForm.php`
- `app/tests/Feature/Profile/ProfileOnboardingTest.php`
- `docs/architektura.md`

## When To Update This File

Update when:

- an onboarding field changes
- the 18+ rule changes
- the photo limit or primary-photo behavior changes
- visibility or moderation semantics change
- matched-profile access changes
- the profile/tag payload changes
