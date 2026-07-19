# Freedateapp MVP Implementation Notes

## Implemented Scope

- Registration/login use the existing Auth module.
- Dating profiles support onboarding, editing, visibility, AI Szarnyseged opt-in, and default matching preferences.
- Preferences store age, distance, gender, orientation, intent, dealbreakers, soft preferences, and AI-enabled profile exclusion.
- Discovery shows one candidate at a time and filters hidden, incomplete, moderated, blocked, previously decided, inactive, deletion-requested, and AI-excluded profiles.
- Accept/reject decisions are persisted. Reciprocal accepts create a matched connection and conversation.
- Matches list only active matched connections. Chat is accessible only to matched participants and supports text messages.
- AI Szarnyseged generates deterministic MVP starter text and posts once to eligible empty matched conversations after the waiting period.
- Report, block, unmatch, deactivate, and deletion-request flows are implemented.
- Admins can review reports, run moderation actions with audit logs, and view aggregate metrics.

## Intentional MVP Limits

- AI starter copy is deterministic and template-based; no external LLM call is made yet.
- Metrics dashboard reads `MetricEvent` aggregates; broader automatic instrumentation can be expanded after launch.
- Account deletion is a request timestamp, not immediate hard deletion.
- UI is Blade-first and design-ready for Figma refinement, not a final polished product design.
- No payment, boost, premium, video, or push-notification code is included.

## Launch Risks

- Matching distance uses approximate latitude/longitude and PHP-side filtering; geospatial indexing should be added before high traffic.
- Admin moderation actions are intentionally minimal and should receive policy-specific review before public launch.
- Assistant humor needs a dedicated copy/safety pass before enabling at scale.
