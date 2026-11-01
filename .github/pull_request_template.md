## Summary

<!-- What changed and why? Keep it short. -->

## Issue

Closes #<!-- issue number -->

## Changes

- <!-- bullet list of the meaningful changes -->

## Verification

- [ ] `docker compose exec php composer test`
- [ ] `docker compose exec php composer phpstan`
- [ ] `docker compose exec php composer cs-check`
- [ ] `docker compose exec php php artisan route:list` (if routes changed)
- [ ] `git diff --check`
- [ ] Figma + Playwright visual check (if UI changed)
- [ ] Independent QA acceptance report (if the agent workflow was used)

### Evidence

<!-- Paste command results, test counts, screenshot paths, or a short note. -->

## Screenshots / UI

<!-- Desktop and mobile screenshots when the UI changed. Remove if not applicable. -->

## Risks / Notes

<!-- Known gaps, intentional follow-ups, or deployment notes. -->

## Checklist

- [ ] The branch is named `<issue>-<slug>` and targets `main`.
- [ ] The commit has a `Changelog:` trailer.
- [ ] The change follows `docs/kod-konvencio.md` and the relevant skills.
- [ ] The affected domain `docs/context.md` is updated if behavior changed.
- [ ] Security review is recorded if auth, visibility, location, reporting, or
      moderation changed.
