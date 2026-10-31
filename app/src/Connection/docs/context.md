# Connection Domain Context

## Purpose

Owns matched connections: the match list and unmatch behavior. A connection
records the two profiles, their decisions, and the match state.

It does not own discovery decisions, conversations, or user account state.

## Core Responsibilities

- list matched connections for a viewer
- filter out ineligible other profiles
- unmatch a connection
- expose match state to chat and profile access

## Main Entry Points

### Presentation

- `app/src/Connection/Presentation/Routes/routes.php`
- `ListMatchesController`, `UnmatchConnectionController`

### Request Handlers

- `app/src/Connection/RequestHandler/ListMatches/`
- `app/src/Connection/RequestHandler/UnmatchConnection/`

### Domain Services

- `ListMatchesService`
- `UnmatchConnectionService`

### Livewire

- `app/app/Livewire/Connection/MatchList.php`

## Important Services

### `ListMatchesService`

- Loads matched connections for the viewer profile.
- Skips the other profile unless it is visible, approved, adult, and its user
  is active and verified.
- Returns match list items for the UI.

### `UnmatchConnectionService`

- Marks the connection as unmatched for the viewer.

## Business Rules

- Only `status = matched` connections appear in the match list.
- The other profile must be `visible` and `approved`.
- The other user must have an active account and a verified email.
- The other profile must satisfy the 18+ rule.
- A block moves the connection to `blocked` (see `Safety`).

## Dependencies

Internal:

- `App\Models\Connection`
- `App\Models\DatingProfile`
- `App\Models\User`
- `App\Profile\Support\AdultEligibility`

External:

- None.

## Data / Ownership

Owns:

- connection status and match state
- unmatch behavior

Does not own:

- discovery decisions (`Discovery`)
- conversations and messages (`Chat`)
- blocks and reports (`Safety`)

## Permissions / Capability Model

- `auth` + `account.active` + `verified` + `dating.profile`.
- A connection is only visible to its two participants.

## Known Constraints

- The profile pair is stored in a deterministic order.
- The match list is filtered in PHP after loading the connections.
- There is no hard delete; unmatch/block change status.

## Gotchas

- Always sort the profile pair before lookup or creation.
- Do not create a conversation from the match list; discovery owns that
  transition.
- A blocked connection must not reappear as matched.
- Keep `matched_at` stable; do not overwrite it on unrelated updates.

## Related Files

- `app/src/Connection/Presentation/Routes/routes.php`
- `app/src/Connection/DomainService/ListMatchesService.php`
- `app/src/Connection/DomainService/UnmatchConnectionService.php`
- `app/app/Models/Connection.php`
- `app/app/Livewire/Connection/MatchList.php`
- `docs/architektura.md`

## When To Update This File

Update when:

- match eligibility changes
- connection statuses change
- unmatch behavior changes
- the match list payload changes
- block/unmatch interaction changes
