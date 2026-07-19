---
name: laravel-cqrs-conventions
description: 'Structural and coding conventions for this repo''s hand-rolled Laravel CQRS/DDD architecture under app/src (Commands, Queries, Handlers, DomainServices, Repositories, Value Objects, Controllers). Use when adding a new use case, module, or domain to the freedateapp Laravel backend, or when writing or reviewing PHP code under app/src or app/app.'
---

# Laravel CQRS Conventions

Adapted from the reference implementation's `.windsurf/rules/laravel-developer.md`. For how the bus itself resolves handlers and how routes are auto-discovered, see `AGENTS.md` at the repo root first. This skill covers the per-file structural rules that sit on top of that mechanism.

## Layer responsibilities

Each module under `app/src/<Module>/` keeps these concerns strictly separate:

- **`RequestHandler/<UseCase>/`** — one `{UseCase}Command.php` (or `Query.php`), its `{UseCase}CommandHandler.php`, and a `{UseCase}CommandResponse.php`. The Command/Query is a self-constructing, self-validating DTO (`SelfConstructingRequestInterface::createFromArray()`, `ValidateRequestInterface::rules()`). The Handler does **no business logic** — it unwraps the command, calls exactly one DomainService method, and wraps the result in a Response. Reference: `app/src/Auth/RequestHandler/Login/`.
- **`DomainService/`** — plain classes with **only** `__construct()` + `handle()`. All business logic lives here, never in the Handler or Controller. Reference: `app/src/User/DomainService/UpdateProfileService.php`.
- **`Presentation/Controller/`** — single-action controllers (`__invoke()`, never a named action method). A controller's job is: run Laravel's own `$request->validate()` for HTTP-shape validation, dispatch a Command/Query through `CommandBusInterface`/`QueryBusInterface`, catch the relevant domain exceptions, return a response. No business logic here either. Reference: `app/src/Auth/Presentation/Controller/LoginController.php`.
- **`Presentation/Routes/routes.php`** — module-local routes, auto-discovered by `RouteServiceProvider` (glob on `src/*/Presentation/Routes/routes.php`). Never register module routes anywhere else.
- **`Dto/ValueObject/`** — every meaningful scalar gets wrapped in a `final readonly class` that self-validates in its constructor (e.g. `EmailAddress`, `PlainPassword`). Never pass a bare `string $email` or `int $userId` through a Command/Service signature — wrap it.

## Hard rules

- Controllers: `__invoke()` only.
- Services: `__construct()` + `handle()` only — no other public methods.
- No DB/Eloquent queries inside a DomainService. If a module needs one, add a `Dto/Repository/{X}RepositoryInterface.php` + an Eloquent implementation, and bind it in `CqrsServiceProvider::register()`. (Prefer calling Eloquent models directly from the DomainService when the model *is* the aggregate root and no cross-cutting query logic is needed — that's what `Auth`/`User` currently do against `App\Models\User`. Only introduce a repository when there's real query complexity to hide.)
- Never inject one repository into another (avoids container resolution loops).
- Every property on a Value Object or Entity is strictly typed — no bare `int`/`string` standing in for a domain identifier.
- No inline comments. DocBlocks only where the *why* isn't obvious from the code (this matches the project-wide rule in the root `CLAUDE.md`).
- Nullable constructor parameters go last, with a default of `null`.
- Backed enums only; case names UPPERCASE.
- When you change a Model/Entity's shape, update the matching migration, and the seeder/factory if one references it.
- Match new foreign key column types exactly against the existing schema (e.g. `users.id` is an unsigned bigint — `user_id` columns must match).

## Adding a new module or use case

1. Create `app/src/<Module>/{RequestHandler,DomainService,Presentation/Controller,Presentation/Routes,Dto/ValueObject}` following the shape above.
2. Write the Command/Query DTO first (self-constructing + `rules()`), then the DomainService method it needs, then the thin Handler, then the Controller, then the route.
3. Nothing needs registering for the bus to find the handler — it resolves `{CommandClass}Handler` by convention. Only add a binding to `CqrsServiceProvider` if the module introduces a new repository interface.
4. Run `docker compose exec php php artisan route:list` to confirm the new route registered.

## Shared base classes that don't exist yet

The reference repo's rules mention a few shared base types this skeleton hasn't needed yet. They are **not present in `app/src/Shared/` today** — don't reference them as if they exist. Create them the first time a second module genuinely needs the shared behavior, in `app/src/Shared/Dto/ValueObject/` or `app/src/Shared/Dto/Collection/`:

- **`Id` base class** — an abstract typed-identifier VO that concrete IDs (`AdminId`, `ProductId`, ...) extend, instead of every module hand-rolling its own `__construct(private string $value)` boilerplate.
- **`GenericCollection`** — base class for a non-paginated typed collection of Entities/DTOs.
- **`PagedCollection`** — base class for a paginated typed collection (wraps page/perPage/total, distinct from Laravel's own `LengthAwarePaginator` which is fine to use directly in `Presentation` for Blade views, as `AdminUserIndexController` already does).
- **`EnumTrait`** — shared trait for backed enums (e.g. label/description lookup helpers), if enum boilerplate starts repeating across modules.

Don't build these speculatively — add each one the first time it's actually needed, matching the shape implied by its first two real use sites.
