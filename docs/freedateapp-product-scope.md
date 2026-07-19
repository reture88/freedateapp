# Freedateapp Product Scope

## Purpose

Freedateapp is a simple, safe, intent-focused dating app. The product should help
users create a meaningful profile, discover compatible people one at a time,
connect only after mutual interest, and start conversations without unnecessary
friction. The first version has no payments, boosts, premium visibility, or
transactional features.

## Product Principles

- Keep the user experience calm and easy to understand.
- Prioritize trust, safety, privacy, and clear consent.
- Optimize for meaningful matches instead of endless browsing.
- Avoid hidden ranking advantages, paid exposure, or manipulative urgency.
- Make every sensitive feature optional and understandable.

## User Onboarding

Registration should collect only what is needed to create a useful dating
profile and operate safely. Required fields should include email, password, age
confirmation, display name, core dating intent, and enough profile data to make
matching possible. Users must confirm they are at least 18 years old.

The onboarding flow should feel like a short profile wizard:

- Basic identity: display name, age, gender, orientation, city or approximate
  location.
- Dating intent: relationship type, seriousness, pace, and what the user is open
  to.
- Profile content: photos, short bio, prompts, interests, values, and lifestyle.
- Preferences: preferred age range, gender/orientation compatibility, distance,
  relationship intent, and a small number of dealbreakers.
- Safety and privacy consent: clear explanation for sensitive data such as
  sexual orientation, sex life, health, religion, ethnicity, or other protected
  categories if collected.

Incomplete profiles should be saved, but not shown in discovery until they meet
the minimum profile quality threshold.

## Profile Model

Each user should have one active dating profile. The profile should separate
private account data from public dating data.

Public or match-visible fields may include:

- Display name, age, approximate location, and main profile photo.
- Gallery photos.
- Bio and profile prompts.
- Interests, languages, values, lifestyle markers, and relationship intent.
- Compatibility highlights such as shared interests or aligned preferences.

Private fields may include:

- Email, authentication details, exact location, moderation notes, consent
  records, and account status.

Users should be able to edit their profile, hide it from discovery, deactivate
their account, or request deletion.

## Preferences and Filters

Preferences should be powerful enough to produce relevant suggestions, but not
so detailed that onboarding becomes tiring.

Hard filters:

- Age range.
- Distance or location radius.
- Gender/orientation compatibility.
- Relationship intent.
- Visibility and safety preferences.

Soft ranking signals:

- Shared interests.
- Similar values.
- Lifestyle compatibility.
- Recent activity.
- Profile completeness and trust indicators.

Dealbreakers should be limited. Too many hard exclusions can make the app feel
empty and brittle.

## Discovery Experience

Discovery should present one profile at a time. The user sees a clear profile
card and chooses either accept or reject.

The card should show:

- Main photo and basic identity.
- Distance or approximate location.
- Relationship intent.
- Short bio or prompt answers.
- Shared interests and compatibility hints.
- Trust indicators, such as verified or complete profile status when available.

Rejecting a profile removes it from the normal queue. Accepting a profile creates
a pending interest. No conversation starts until both sides accept each other.

## Match and Connection Flow

A connection is created only when both profiles accept each other.

States:

- `pending`: one user accepted the other.
- `matched`: both users accepted.
- `unmatched`: at least one user ended the connection.
- `blocked`: one user blocked the other.

After a match, both users should see a simple entry point into the conversation.
The app should encourage a first message without making the interaction feel
forced.

## Messaging

Messaging is available only after a mutual match. The first version should
support text messages, message timestamps, basic read state if desired, and a
clear way to report, block, or unmatch from the conversation.

The product should discourage unsafe behavior:

- Warn users about requests for money, crypto, investments, gift cards, or urgent
  off-platform contact.
- Provide one-tap report actions for suspicious messages.
- Make blocking and unmatching easy and final from the user's perspective.

## AI Szárnysegéd

AI Szárnysegéd is an optional conversation-starting assistant for matched users.
Its purpose is to reduce awkward silence after a mutual match by posting a
light, humorous, human-feeling icebreaker into an otherwise empty conversation.

Core behavior:

- The feature is opt-in per profile.
- If enabled and a matched conversation remains empty for a configured waiting
  period, the assistant may post one humorous starter message.
- The assistant appears as a third participant or clearly labeled system persona,
  not as either user.
- The message should reference safe, visible profile details from both users,
  such as interests, pets, hobbies, or prompt answers.
- The message should invite one user to ask the other something easy and playful.
- The assistant should not keep chatting continuously in v1; it only nudges the
  conversation once unless a later product decision expands it.

Example tone:

> Sanyi, láttad, hogy Marinak három macskája van? Én a helyedben megkérdezném,
> lehet-e jelentkezni negyediknek. Haha, csak óvatosan. Beszélgessetek,
> fiatalok.

User controls:

- Users can enable or disable AI Szárnysegéd on their own profile.
- Users can set a preference to exclude profiles where AI Szárnysegéd is active.
- Discovery should respect that preference and not show those profiles.
- Users should be able to hide or dismiss the assistant message in a chat.

Safety and quality rules:

- Do not mention sensitive attributes unless the user explicitly made them public
  and consented to this use.
- Do not make sexual, insulting, manipulative, discriminatory, or pressure-based
  jokes.
- Do not imply personal knowledge that is not visible in the profiles.
- Do not fabricate facts.
- Avoid repetitive templates; the long-term goal is a dedicated humorous
  personality skill set with natural, warm, context-aware jokes.
- Provide report feedback if an assistant message feels inappropriate.

The feature should be positioned as playful help, not automation pretending to be
a real person.

## Trust, Safety, and Moderation

Safety is a foundation feature, not an add-on.

Required capabilities:

- Report profile.
- Report message.
- Block user.
- Unmatch.
- Hide profile from discovery.
- Admin review queue for reports.
- Account states: active, hidden, suspended, banned, deleted.
- Audit log for moderation decisions.

Future trust features:

- Photo verification.
- Selfie or liveness verification.
- Duplicate-image and fake-profile detection.
- Scam-pattern monitoring.
- Optional verified badge.

## Privacy and Data Protection

Dating profiles may contain sensitive personal data. The app must give users
clear control over what they share, how it is used, and who can see it.

Requirements:

- Explicit consent for sensitive data.
- Separate public profile data from private account data.
- Show approximate location only, never exact location.
- Let users edit or remove profile fields.
- Let users deactivate or delete their account.
- Keep moderation and security logs access-controlled.
- Avoid collecting fields that are not needed for matching, safety, or account
  operation.

## Admin and Operations

The app needs a practical admin layer from the beginning.

Admin functions:

- Search and inspect users.
- View profile status and completion.
- Review reports.
- Suspend, ban, restore, or delete users.
- Inspect match and chat metadata when needed for moderation.
- Track audit logs.

Operational metrics:

- New registrations.
- Completed profiles.
- Active users.
- Accept/reject rates.
- Match rate.
- First-message rate.
- Report rate.
- Block/unmatch rate.
- AI Szárnysegéd opt-in rate and starter success rate.

## MVP Scope

The first production-capable version should include:

- Registration and login.
- Profile onboarding and editing.
- Preferences and discovery filters.
- One-profile-at-a-time discovery.
- Accept/reject actions.
- Mutual match creation.
- Matched-only messaging.
- AI Szárnysegéd opt-in and one-time starter message.
- Preference to exclude AI Szárnysegéd-enabled profiles.
- Report, block, unmatch, and account hiding.
- Basic admin moderation.
- Account deactivation and deletion.

Later versions can add:

- Automated photo verification.
- Push notifications.
- Smarter compatibility scoring.
- Video calls.
- Events or group discovery.
- Premium subscriptions, boosts, or payments.
- More advanced AI conversation coaching.
