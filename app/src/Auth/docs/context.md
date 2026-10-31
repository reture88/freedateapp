# Auth Domain Context

## Purpose

Owns authentication and session entry: registration, login, logout, email
verification, password reset, welcome, account-unavailable, and the routing
decision after authentication.

It does not own account data, account status values, or profile content; those
belong to `User` and `Profile`.

## Core Responsibilities

- register a user and start a session
- authenticate active users
- let restricted users reach the account-status page with valid credentials
- verify email addresses and resend verification mail
- request and complete password resets
- decide the post-login/post-verification destination

## Main Entry Points

### Presentation

- `app/src/Auth/Presentation/Routes/routes.php`
- Controllers in `app/src/Auth/Presentation/Controller/`
- Views in `app/resources/views/auth/`

### Request Handlers

- `app/src/Auth/RequestHandler/Login/`
- `app/src/Auth/RequestHandler/Logout/`
- `app/src/Auth/RequestHandler/Register/`

### Domain Services

- `LoginService`
- `LogoutService`
- `RegisterUserService`
- `AuthDestinationService`

### Laravel Integration

- `app/src/Auth/Laravel/ServiceProvider.php`

## Important Services

### `LoginService`

- Looks up the user by email.
- If the password is valid but the account is not active, logs the user in and
  returns the user so the controller can route to the status page.
- Otherwise uses `Auth::attempt` with `account_status = active`.
- Regenerates the session on success.
- Throws `InvalidCredentialsException` for invalid credentials.

### `RegisterUserService`

- Checks password confirmation and unique email.
- Creates an `ACTIVE` user with `is_active = true`.
- Fires `Illuminate\Auth\Events\Registered` (sends the verification mail).
- Logs the user in and regenerates the session.

### `AuthDestinationService`

Single destination rule:

1. inactive account -> `auth.account-unavailable`
2. unverified email -> `verification.notice`
3. no dating profile -> `auth.welcome`
4. otherwise -> `discovery.show`

### `LogoutService`

- `Auth::logout()`
- invalidate the session
- regenerate the CSRF token

## Business Rules

- Login is rate-limited to 10 attempts per minute; registration to 8.
- Verification resend is limited to 6 per minute; password reset to 5.
- Password reset requires at least 8 characters and a matching confirmation.
- Forgot-password always returns the same privacy-safe status, whether or not
  the email exists.
- Verification links are signed URLs and use the `verification.verify` route.
- The `auth/account-unavailable` page is reachable without `auth`; the
  controller handles guests.

## Dependencies

Internal:

- `App\Models\User`
- `App\User\Dto\ValueObject\EmailAddress`
- `App\User\Dto\ValueObject\PlainPassword`
- `App\User\Dto\ValueObject\Name`
- `App\User\Enum\AccountStatus`
- `App\Models\DatingProfile`

External:

- Laravel `Auth`, `Password`, `Hash`, `Session`, `Notification`
- Mail transport (`MAIL_MAILER`)

## Data / Ownership

Owns:

- session entry and exit
- verification and password-reset flows
- post-authentication destination

Does not own:

- account status values (`User`)
- profile existence (`Profile`)
- admin moderation (`User`)

## Permissions / Capability Model

- `guest`: login, register, forgot/reset password.
- `auth`: verification prompt, resend, verify, logout.
- `auth` + `account.active`: verification prompt/resend/verify, welcome.
- `auth` + `account.active` + `verified`: welcome.
- `account.active` before `verified` is intentional.

## Known Constraints

- Email verification needs a working mail configuration; local default is
  `log`.
- Password reset uses the `password_reset_tokens` table.
- The verification route requires the `signed` middleware.
- The account-unavailable route is public but reads the authenticated user
  when present.

## Gotchas

- `LoginService` deliberately logs in a non-active user with valid
  credentials; do not "fix" this by rejecting the login. The status page is
  the intended UX.
- Do not duplicate destination logic in controllers; call
  `AuthDestinationService`.
- Do not put `verified` before `account.active`; it would hide the account
  status page behind the verification prompt.
- The GET and POST logout routes both exist; keep them consistent.
- `Registered` must stay dispatched on registration, otherwise no verification
  mail is sent.

## Related Files

- `app/src/Auth/Presentation/Routes/routes.php`
- `app/src/Auth/DomainService/LoginService.php`
- `app/src/Auth/DomainService/RegisterUserService.php`
- `app/src/Auth/DomainService/LogoutService.php`
- `app/src/Auth/DomainService/AuthDestinationService.php`
- `app/app/Models/User.php`
- `app/app/Http/Middleware/EnsureAccountIsActive.php`
- `app/tests/Feature/Auth/AuthenticationFlowTest.php`
- `docs/architektura.md`

## When To Update This File

Update when:

- a login/registration/verification/reset rule changes
- a route or middleware changes
- the destination order changes
- a rate limit changes
- a new auth integration is introduced
