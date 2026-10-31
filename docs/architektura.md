# Freedateapp Architecture

This is the system map for agents. It is intentionally shorter than
`docs/freedateapp-technical-spec.md`; read that document for product and data
detail.

## Runtime

- nginx -> php-fpm (Laravel 12), MariaDB, Redis, Reverb.
- App root: `app/`.
- Framework glue: `app/app/`.
- Domain modules: `app/src/<Module>/`.
- UI: Blade + Livewire 3 + Filament 4, plain CSS.

## CQRS/DDD Request Flow

1. Route in `app/src/<Module>/Presentation/Routes/routes.php`.
2. Single-action controller (`__invoke`) collects the payload and context.
3. Controller dispatches a Command/Query through `CommandBusInterface` or
   `QueryBusInterface`.
4. `GenericBus` validates the payload when the request implements
   `ValidateRequestInterface`, then calls `createFromArray()`.
5. `{UseCase}CommandHandler` / `{UseCase}QueryHandler` calls one
   DomainService method and wraps the result in a Response.
6. DomainService owns business logic and persistence.
7. Controllers convert expected business exceptions into the existing error
   response pattern; framework errors stay in the global handler.

Module routes are auto-discovered by
`app/app/Providers/RouteServiceProvider.php` via
`glob(base_path('src/*/Presentation/Routes/routes.php'))`.

## Module Map

| Module | Responsibility | Main entry points |
| --- | --- | --- |
| `Assistant` | AI wingman starter messages for matched conversations | `AssistantStarterService`, `PostAssistantStartersService`, `assistant:post-wingman-starters` |
| `Auth` | Registration, login, logout, email verification, password reset, welcome and account-status entry | `app/src/Auth/Presentation/Routes/routes.php`, `LoginService`, `RegisterUserService`, `AuthDestinationService` |
| `Chat` | Matched conversation display, messages, read state | `ShowConversationService`, `SendMessageService`, `MarkConversationReadService`, `ConversationPanel` |
| `Connection` | Matched connections list and unmatch | `ListMatchesService`, `UnmatchConnectionService`, `MatchList` |
| `Discovery` | One-card candidate selection and accept/reject decisions | `FindNextCandidateService`, `RecordDiscoveryDecisionService`, `DiscoveryCard` |
| `Legal` | Public terms and privacy pages | `ShowTermsController`, `ShowPrivacyController` |
| `Metrics` | Metric event recording and admin dashboard | `MetricRecorderService`, `MetricsDashboardService`, `AdminMetricsDashboardController` |
| `Preference` | Dating preferences and defaults | `EnsureDefaultPreferenceService`, `SavePreferenceService`, `BuildPreferenceSummaryService` |
| `Profile` | Dating profile onboarding, photos, visibility, preview, matched profile | `SaveDatingProfileOnboardingService`, `UploadProfilePhotoService`, `ShowMatchedProfileService` |
| `Safety` | Reports, blocks, admin report moderation | `SubmitReportService`, `BlockUserService`, `AdminModerateReportService` |
| `Shared` | Bus, container, validation, translation, common DTOs | `GenericBus`, `InMemoryCommandBus`, `InMemoryQueryBus`, `Translator`, `SimpleValidator` |
| `User` | Account identity, profile settings, addresses, password, deactivate/delete, admin user management, account status | `app/src/User/Presentation/Routes/routes.php`, `AccountStatus`, `AdminModerateUserService` |

## Access Gates

| Middleware | Meaning |
| --- | --- |
| `auth` | Authenticated user |
| `account.active` | `AccountStatus::ACTIVE` |
| `verified` | Verified email address |
| `dating.profile` | Completed dating profile and 18+ birthdate |
| `admin` | Admin user or admin role |
| `guest` | No authenticated user |

Order matters: `account.active` runs before `verified`, so restricted accounts
see the account-status page instead of the verification prompt.

## Frontend Boundaries

- Livewire components: `app/app/Livewire/<Area>/`.
- Livewire views: `app/resources/views/livewire/<area>/`.
- Blade layouts and partials: `app/resources/views/layouts`,
  `app/resources/views/components`, `app/resources/views/shared`.
- Filament admin: `app/app/Filament/`; simple reads may use Eloquent,
  mutations call services or command handlers.
- CSS: `app/public/css/app.css`; new UI uses the `mtam-` design-system
  prefix.
- No Node/Vite/Vue/Inertia build step.

## Data Ownership

| Area | Owns | Does not own |
| --- | --- | --- |
| `User` | Account identity, status, addresses, password, admin role | Dating profile content |
| `Profile` | Dating profile, photos, tags, visibility | Account status, preferences |
| `Preference` | Dating preferences and defaults | Discovery ranking |
| `Discovery` | Per-viewer decisions | Connection state |
| `Connection` | Match state and unmatch | Conversation messages |
| `Chat` | Conversations, messages, read state | Match eligibility |
| `Safety` | Reports, blocks, moderation audit | Account status changes (User owns) |
| `Metrics` | Metric events and aggregates | Product decisions |
| `Assistant` | Assistant starter message creation | Chat transport |

## Realtime

Events:

- `MatchCreated` -> `users.{userId}` private channel.
- `MessageSent` -> `conversations.{conversationId}` private channel.
- `MessagesRead` -> `conversations.{conversationId}` private channel.
- `ReportSubmitted` -> admin reports channel.

Livewire listeners update the match list and conversation panel.

## Related Docs

- `docs/freedateapp-technical-spec.md`
- `docs/freedateapp-product-scope.md`
- `docs/kod-konvencio.md`
- `docs/freedateapp-figma-design-brief.md`
- `docs/harness.md`

## When To Update This File

Update when a module boundary, route group, access gate, cross-domain
dependency, data ownership, or realtime contract changes.
