---
name: delivery-workflow
description: 'Use when starting or finishing a task: create/find a GitHub Issue, branch from main, commit with a Changelog trailer, push, open a pull request, sync with main, or close out the day. Use for any code or docs change that should land through the GitHub flow.'
---

# Delivery Workflow (GitHub)

Every feature or bug goes through a GitHub Issue, a feature branch, and a pull
request. Do not commit code directly to `main`.

## 1. Issue first

1. Search existing issues before creating a new one.
2. For a feature or bug, create or reuse an Issue.
3. Title format:
   - `[BE] - <Module> - <Short task>`
   - `[FE] - <Module> - <Short task>`
   - `[QA] - <Module> - <Short task>`
   - `[FEBE] - <Module> - <Short task>`
4. Issue body is English and includes:
   - context and current behavior,
   - expected behavior,
   - scope and constraints,
   - acceptance criteria,
   - the affected module(s),
   - the Figma frame or design link for UI work.
5. Ask for approval before creating or updating an Issue.
6. If `gh` is unavailable, use the GitHub web UI and record the Issue URL; do
   not invent an Issue number.

## 2. Branch

1. `git checkout main`
2. `git pull origin main`
3. `git checkout -b <issue-number>-<kebab-slug>`
   - Example: `42-add-account-lifecycle`.
   - No `feature/`, `bugfix/`, `hotfix/`, or `codex/` prefix.
4. If the Issue number cannot be resolved, stop and report it.

## 3. Commit

Format:

```text
<short imperative English subject>

<optional body: what changed and why>

Changelog: <type>
```

Allowed `type` values: `added`, `changed`, `deprecated`, `removed`, `fixed`,
`security`.

- One logical change per commit.
- Stage only the files that belong to the change.
- Do not commit secrets, `.env`, tokens, or generated cache files.
- Do not amend or force push a shared branch.

## 4. Before the pull request

Run the definition of done:

- `docker compose exec php composer test`
- `docker compose exec php composer phpstan`
- `docker compose exec php composer cs-check` (then `cs-fix` after green)
- `docker compose exec php php artisan route:list` for route changes
- `git diff --check`
- Figma + Playwright visual verification for UI changes

If a check fails, stop and fix it. Do not open or update the pull request with
a known failure unless the user explicitly overrides; document the failure in
the testing section.

## 5. Pull request

1. `git push -u origin <branch>`
2. Create the pull request with `gh pr create` when available; otherwise use
   the GitHub compare URL.
3. Use `.github/pull_request_template.md`.
4. Title: short and imperative.
5. Body:
   - summary,
   - `Closes #<issue>`,
   - changes,
   - verification evidence (commands, results, screenshots),
   - remaining risks or gaps.
6. Keep the branch up to date by merging `main` into it:
   `git merge main`; resolve conflicts manually; never force push.
7. Delete the branch after merge.

## 6. End of day

- Commit and push the current branch.
- Use the same commit format.
- If the work is not ready for a pull request, say so; do not open a draft PR
  without the user's approval.

## When `gh` is not installed

- Use the GitHub web UI for Issues and pull requests.
- Record the Issue/PR URL in the task notes or commit body.
- Do not fabricate an Issue number for the branch name; ask the user for the
  number or use a clearly temporary bootstrap branch and rename it after the
  Issue exists.

## Related

- `laravel-definition-of-done` — quality gates.
- `planning` — plan before implementation.
- `figma-playwright-verification` — UI evidence.
- `.github/pull_request_template.md`
- `.github/ISSUE_TEMPLATE/`
