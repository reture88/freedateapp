# Freedateapp Figma Design Brief

## Purpose

This brief captures the product, UX, content, and component information needed
to create Figma design plans later through a Figma MCP workflow. It intentionally
does not prescribe implementation code.

## Product Feel

Freedateapp should feel simple, warm, safe, and low-friction. The interface
should avoid noisy gamification, pressure tactics, dark patterns, and overloaded
settings. The user should always understand what happens next.

Design traits:

- Mobile-first for dating flows.
- Desktop-friendly for admin/moderation.
- Calm visual hierarchy.
- Clear primary actions.
- Human copy, short labels, no marketing-heavy hero sections inside the app.
- Trust and privacy controls visible but not alarming.

## Primary User Flows

### 1. Registration to Completed Profile

Screens:

- Register
- Login
- Age confirmation
- Profile basics
- Photos
- Bio and prompts
- Interests and lifestyle
- Dating intent
- Preferences
- Privacy and consent
- Profile preview
- Completion success

Key UX needs:

- Show progress.
- Save partial progress.
- Keep each step short.
- Explain sensitive data plainly.
- Do not show incomplete profiles in discovery.

### 2. Discovery to Match

Screens:

- Discovery card
- Expanded profile details
- Reject confirmation only if needed for accidental taps
- Match success
- No candidates empty state
- Preferences shortcut

Key UX needs:

- One profile at a time.
- Accept/reject actions thumb-friendly on mobile.
- Profile facts easy to scan.
- Shared interests and intent visible.
- Rejection should be quick and low-drama.

### 3. Match to Chat

Screens:

- Match list
- Conversation
- Empty conversation state
- AI Szárnysegéd starter state
- Report/block/unmatch modal

Key UX needs:

- Chat only after mutual match.
- Empty conversation should feel inviting.
- Assistant message must be clearly labeled as AI/system persona.
- Report/block/unmatch available without crowding the chat.

### 4. Profile and Settings

Screens:

- My profile
- Edit profile
- Edit preferences
- Privacy controls
- AI Szárnysegéd toggle
- Exclude AI Szárnysegéd profiles toggle
- Hide profile
- Deactivate account
- Delete account

Key UX needs:

- Separate “how I appear” from “who I want to meet.”
- Make visibility status obvious.
- Make destructive actions clearly confirmed.

### 5. Admin Moderation

Screens:

- Admin dashboard
- User list
- Profile detail
- Report queue
- Report detail
- Audit log
- Metrics overview

Key UX needs:

- Dense, scannable tables.
- Clear status badges.
- Fast moderation actions.
- Report context visible without exposing unnecessary private data.

## Core Components

## Implementation Target

Designs should assume the app frontend is implemented with Livewire for
authenticated user flows, Filament for admin and moderation flows, and Laravel
Reverb for realtime interaction. Figma annotations should name expected
component states, realtime events, and admin resource surfaces where relevant.

### App Shell

- Public layout for auth and basic pages.
- Authenticated user layout with navigation.
- Admin layout with sidebar navigation and dense content area.

### Profile Card

Variants:

- Discovery card
- Compact match card
- Admin profile summary
- Profile preview

Required content:

- Main photo
- Display name and age
- Approximate location
- Relationship intent
- Bio excerpt
- Shared interests
- Status badges

### Action Buttons

Use icon-first controls where appropriate:

- Accept
- Reject
- Back
- Edit
- Save
- Report
- Block
- Unmatch
- Settings

Buttons need disabled, loading, active, destructive, and confirmation states.

### Forms

Components:

- Text input
- Textarea
- Select
- Multi-select tags
- Toggle
- Checkbox
- Radio group
- Slider or numeric range for age/distance
- Photo uploader
- Validation message

Form guidance:

- Keep labels direct.
- Put helper text near sensitive fields.
- Validate inline and on submit.

### Chat

Components:

- User message bubble
- Other user message bubble
- Assistant message bubble
- Timestamp
- Empty conversation prompt
- Composer
- Safety/report menu
- Incoming message state
- Sending and failed-send state
- Reconnect indicator

Assistant bubble requirements:

- Clearly says AI Szárnysegéd.
- Visually distinct from user bubbles.
- Includes dismiss action.
- Never looks like one matched user wrote it.

### Moderation Components

- Status badge
- Report category badge
- Risk indicator
- Timeline/audit entry
- Admin action menu
- Confirmation modal
- Notes field

## Required States

Design all important screens with these states:

- Loading
- Empty
- Error
- Validation error
- Unauthorized
- Hidden profile
- Suspended profile
- No discovery candidates
- Match created
- Conversation empty
- Assistant starter posted
- User blocked
- User unmatched
- Report submitted

## Content Guidelines

Voice:

- Friendly, short, direct.
- Avoid shame, pressure, and urgency.
- Explain privacy and safety without legal-heavy language.

Example labels:

- “Show my profile”
- “Pause discovery”
- “Only show people without AI Szárnysegéd”
- “Report this profile”
- “End this match”
- “You matched. Start with something simple.”

AI Szárnysegéd tone:

- Playful, warm, never crude.
- Uses visible profile facts only.
- Does not flirt on behalf of users.
- Does not pressure either user to reply.

Example:

> Sanyi, Mari három macskával érkezik a beszélgetésbe. Én óvatosan nyitnék:
> “Hogy hívják a főnököt közülük?” Beszélgessetek, fiatalok.

## Visual System Direction

Recommended direction:

- Light default theme.
- Soft neutral base with warm accent color.
- Rounded but not toy-like controls.
- Large photos in discovery, compact density in admin.
- Clear iconography for accept/reject/report/block.
- Minimal animation: card transitions, match success, chat message arrival.

Avoid:

- Casino-like swipe urgency.
- Heavy purple-blue gradients as dominant theme.
- Decorative bokeh/orb backgrounds.
- Marketing hero layouts inside app workflows.
- Text explaining basic controls that should be obvious.

## Figma MCP Deliverables

When creating Figma plans later, generate:

- Information architecture map.
- User flow diagrams for onboarding, discovery, match, chat, report/block, and
  admin moderation.
- Mobile screen set for user workflows.
- Desktop screen set for admin workflows.
- Component library with variants and states.
- Prototype path from registration to first match and first chat.
- Design annotations for privacy, safety, and AI Szárnysegéd behavior.
- Livewire component inventory for onboarding, profile, discovery, matches,
  chat, and settings.
- Filament resource/page inventory for users, profiles, reports, audit logs, and
  metrics.
- Reverb event map for match creation, incoming message, read state, assistant
  starter, and new moderation report.

## Acceptance Criteria for Design Phase

Design work is ready for implementation when:

- Every MVP screen has mobile layout.
- Admin screens have desktop layout.
- Every core component has normal, loading, error, and disabled/destructive
  states where applicable.
- AI Szárnysegéd is visually distinct and consent-aware.
- Report/block/unmatch actions are accessible from profile and chat contexts.
- Empty states explain next action without sounding like marketing copy.
