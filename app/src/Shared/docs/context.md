# Shared Domain Context

## Purpose

Owns the cross-cutting CQRS infrastructure and common DTOs: the bus,
container adapter, validation, translation, pagination/filter/sort objects,
and error-response helpers.

It does not own module business rules.

## Core Responsibilities

- validate and construct Commands/Queries
- resolve the matching Handler by convention
- adapt the Laravel container
- provide domain translation helpers
- provide common value objects and query DTOs
- provide the controller error-response trait

## Main Entry Points

### Bus

- `app/src/Shared/Infrastructure/Bus/GenericBus.php`
- `app/src/Shared/DomainService/Bus/InMemoryCommandBus.php`
- `app/src/Shared/DomainService/Bus/InMemoryQueryBus.php`
- Interfaces under `app/src/Shared/Infrastructure/Bus/`

### Validation

- `app/src/Shared/Infrastructure/Validation/SimpleValidator.php`
- `app/src/Shared/Infrastructure/Validation/ValidatorInterface.php`
- `app/src/Shared/Infrastructure/Validation/ValidationException.php`

### Translation

- `app/src/Shared/Infrastructure/Translator/Translator.php`

### DTOs

- `app/src/Shared/Dto/Pagination/Pagination.php`
- `app/src/Shared/Dto/Query/Filter.php`
- `app/src/Shared/Dto/Query/Sort.php`
- `app/src/Shared/Dto/ValueObject/Context/SystemContext.php`
- `app/src/Shared/Dto/ValueObject/Money.php`
- `app/src/Shared/Dto/ValueObject/Slug.php`

### Presentation

- `app/src/Shared/Presentation/Controller/InteractsWithErrorResponses.php`

## Important Services

### `GenericBus`

- If the request implements `ValidateRequestInterface`, validates the payload
  with `rules()`.
- Requires `createFromArray()`; otherwise throws
  `MissingSelfConstructingRequestInterfaceException`.
- Returns a Command/Query.

### `InMemoryCommandBus` / `InMemoryQueryBus`

- Resolve the handler from the container by naming convention.
- Throw `HandlerNotFoundException` when no handler exists.

### `SimpleValidator`

- Implements the project's rule set, including `adult` via
  `AdultEligibility`.
- Throws `ValidationException` with field errors.

### `Translator`

- `transStatic('domain.<module>.<key>')` for domain messages.
- Keeps user-facing domain exceptions translatable.

## Business Rules

- Commands/Queries are self-constructing and self-validating.
- Handlers are resolved by convention: `{CommandClass}Handler`.
- Controllers dispatch through the bus; they do not instantiate handlers.
- Domain exceptions use the translator.
- The `adult` rule delegates to `Profile\Support\AdultEligibility`.

## Dependencies

Internal:

- All modules depend on Shared.
- `Profile\Support\AdultEligibility` for the `adult` validation rule.

External:

- Laravel container, validator, translator, paginator.

## Data / Ownership

Owns:

- bus/container/validation/translation infrastructure
- common DTOs and value objects

Does not own:

- module entities, repositories, or business rules.

## Permissions / Capability Model

- No routes; shared services are injected.

## Known Constraints

- There is no `Id` base class, `GenericCollection`, `PagedCollection`, or
  `EnumTrait` yet. Do not reference them as if they exist.
- There is no repository/transformer layer in this project.
- `SimpleValidator` is a lightweight custom validator, not Laravel's
  `$request->validate()`.
- Bus handler resolution is convention-based, not a registry.

## Gotchas

- A Command/Query without `createFromArray()` fails the bus.
- A Command/Query without `ValidateRequestInterface` skips validation.
- Do not put module-specific validation rules in `SimpleValidator`; keep it
  generic.
- Do not add a repository/transformer layer only for symmetry; add it when
  query complexity needs hiding.
- `InteractsWithErrorResponses` is the controller-side error helper; keep
  module exceptions out of the global renderer only where the pattern exists.

## Related Files

- `app/src/Shared/Infrastructure/Bus/GenericBus.php`
- `app/src/Shared/DomainService/Bus/InMemoryCommandBus.php`
- `app/src/Shared/DomainService/Bus/InMemoryQueryBus.php`
- `app/src/Shared/Infrastructure/Validation/SimpleValidator.php`
- `app/src/Shared/Infrastructure/Translator/Translator.php`
- `app/src/Shared/Presentation/Controller/InteractsWithErrorResponses.php`
- `docs/architektura.md`
- `docs/kod-konvencio.md`

## When To Update This File

Update when:

- the bus contract changes
- validation behavior changes
- translation behavior changes
- a shared DTO is added or changed
- a base class is introduced
