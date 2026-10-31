# User Domain Context

## Purpose

Owns account identity and settings: name, email, password, role/admin flag,
account status, addresses, profile settings, deactivation, deletion request,
and admin user management.

It does not own dating profile content (`Profile`) or authentication flows
(`Auth`).

## Core Responsibilities

- store account identity and status
- expose account status as a typed enum
- let a user update profile settings and password
- save shipping/billing addresses
- deactivate an account
- request account deletion
- let admins list, inspect, update, and moderate users
- write audit logs for account lifecycle actions

## Main Entry Points

### Presentation

- `app/src/User/Presentation/Routes/routes.php`
- `ProfileController`, `UpdateProfileController`, `ChangePasswordController`,
  `SaveAddressController`, `DeactivateAccountController`,
  `RequestAccountDeletionController`
- Admin controllers under
  `app/src/User/Presentation/Controller/Admin/`

### Request Handlers

- `app/src/User/RequestHandler/UpdateProfile/`
- `app/src/User/RequestHandler/ChangePassword/`
- `app/src/User/RequestHandler/SaveAddress/`
- `app/src/User/RequestHandler/DeactivateAccount/`
- `app/src/User/RequestHandler/RequestAccountDeletion/`
- `app/src/User/RequestHandler/AdminModerateUser/`

### Domain Services

- `UpdateProfileService`
- `ChangePasswordService`
- `SaveAddressService`
- `DeactivateAccountService`
- `RequestAccountDeletionService`
- `AdminModerateUserService`

### Enum / Value Objects

- `app/src/User/Enum/AccountStatus.php`
- `app/src/User/Dto/ValueObject/`

## Important Services

### `AccountStatus`

- Cases: `ACTIVE`, `SUSPENDED`, `BANNED`, `DEACTIVATED`,
  `DELETION_REQUESTED`.
- `canUseProduct()` is true only for `ACTIVE`.
- `User::accountStatus()` falls back to `is_active`/`deletion_requested_at`
  for legacy rows.

### `AdminModerateUserService`

- `suspend` -> `SUSPENDED`, `is_active = false`
- `ban` -> `BANNED`, `is_active = false`
- `deactivate` -> `DEACTIVATED`, `is_active = false`
- `restore` -> `ACTIVE`, `is_active = true`, clears
  `deletion_requested_at`
- Writes an `AuditLog` with before/after data.

### `DeactivateAccountService`

- Sets `DEACTIVATED`, `is_active = false`.
- Hides the dating profile.
- Writes an audit log.

### `RequestAccountDeletionService`

- Sets `DELETION_REQUESTED`, `is_active = false`,
  `deletion_requested_at = now()`.
- Hides the dating profile.
- Writes an audit log.

### `ChangePasswordService`

- Checks the current password.
- Checks confirmation.
- Hashes and saves the new password.

### `SaveAddressService`

- Upserts a `UserAddress` by user + type (`shipping` or `billing`).

### `UpdateProfileService`

- Updates the account name and email.
- Rejects an email that another user already owns.
- If the email changes, clears `email_verified_at` and sends a new
  verification notification.

## Business Rules

- Only `ACTIVE` accounts can use product routes (`account.active`).
- `is_active` mirrors `account_status`; keep them consistent.
- A deactivated or deletion-requested account has a hidden dating profile.
- `restore` clears the deletion timestamp.
- Admin updates write an audit log (`update_user_account`).
- Admin panel access requires an active account and admin role/flag.
- Address types are limited to `shipping` and `billing`.
- Changing the account email resets email verification and sends a new
  verification notification.

## Dependencies

Internal:

- `App\Models\User`
- `App\Models\UserAddress`
- `App\Models\AuditLog`
- `App\Models\DatingProfile`
- `App\Profile\Support\AdultEligibility` (indirectly via profile access)
- `App\User\Dto\ValueObject\*`

External:

- Laravel `Hash` for password hashing.
- Filament for the admin panel contract.

## Data / Ownership

Owns:

- `users` account fields and status
- `user_addresses`
- account lifecycle audit entries

Does not own:

- dating profile content (`Profile`)
- preferences (`Preference`)
- authentication/session flow (`Auth`)

## Permissions / Capability Model

- `auth` + `account.active` + `verified` for profile settings, password,
  addresses, deactivate, delete request.
- `auth` + `admin` for `/admin/users`.
- Admins can moderate users only when they are active admins.

## Known Constraints

- Account deletion is a request timestamp, not an immediate hard delete.
- `is_active` is a legacy mirror and must not diverge from `account_status`.
- The admin panel is a Filament panel at `/admin-panel`.
- `LoginService` intentionally logs in non-active users so they can see the
  status page; do not treat that as a bug.

## Gotchas

- Update both `account_status` and `is_active` when changing access.
- `restore` must clear `deletion_requested_at`.
- Do not change account status in `Safety`; call this domain's service.
- `account_status` is cast to the enum; `User::accountStatus()` handles
  legacy rows.
- Deactivate/delete must hide the dating profile.
- Changing the email clears `email_verified_at`; the user must verify again
  before product routes become available.
- Admin updates and moderation must write an audit log.
- Keep the admin panel access check (`canAccessAdminPanel`) aligned with
  `hasActiveAccount()`.

## Related Files

- `app/src/User/Presentation/Routes/routes.php`
- `app/src/User/DomainService/AdminModerateUserService.php`
- `app/src/User/DomainService/DeactivateAccountService.php`
- `app/src/User/DomainService/RequestAccountDeletionService.php`
- `app/src/User/DomainService/ChangePasswordService.php`
- `app/src/User/Enum/AccountStatus.php`
- `app/app/Models/User.php`
- `app/tests/Feature/User/AccountLifecycleTest.php`
- `app/tests/Feature/Admin/ModerationActionTest.php`
- `docs/architektura.md`

## When To Update This File

Update when:

- an account status or transition changes
- a moderation action changes
- profile settings or address behavior changes
- password rules change
- an audit action changes
- admin user management changes
