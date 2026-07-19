# Freedateapp Profile Tags and Matching Specification

## Purpose

Profile tags let users describe public personality signals and private matching
signals without turning onboarding into a long questionnaire. They support three
goals:

- richer profile pages with bio, interests, lifestyle, and values;
- visible shared-interest hints in discovery and conversations;
- soft ranking so profiles with more overlap appear earlier.

The MVP uses a controlled taxonomy instead of free-form tags. This avoids
duplicate wording, makes matching reliable, and keeps moderation manageable.

## Research Notes

Large dating apps separate profile basics from interest/lifestyle badges.
Bumble documents basic badges such as height, drinking, smoking, kids, religion,
language, and politics, and separate interest badges for hobbies and values.
Bumble also describes filtering by shared interest badges. Tinder describes
Interests as selectable profile pastimes used for profile display and
conversation starters, with ordered selection on the profile.

References:

- https://bumble.com/en-us/the-buzz/bumble-badges
- https://bumble.com/en-us/the-buzz/filter-by-interest-badges
- https://www.help.tinder.com/hc/en-us/articles/360046122691-Interests
- https://www.help.tinder.com/hc/en-us/articles/115003339043-Editing-your-profile

## Taxonomy

MVP categories and tags:

- Hobbies: photography, cooking, gardening, board games, DIY, writing, dancing
- Sports and movement: running, gym, yoga, hiking, cycling, swimming, football,
  basketball, tennis, winter sports
- Music: pop, rock, electronic, jazz, classical, concerts, festivals
- Film and TV: sci-fi, fantasy, comedy, thriller, documentary, anime, streaming
- Food and drink: coffee, wine, street food, vegan, baking, restaurants, brunch
- Travel: city breaks, beach, road trips, nature trips, spontaneous travel
- Pets: dogs, cats, pet friendly, no pets, wants pets
- Lifestyle: early bird, night owl, smoke free, rarely drinks, active lifestyle
- Values: family oriented, environmental, ambitious, open minded, humor matters
- Culture: books, museums, theatre, podcasts, art galleries
- Staying in: gaming, movie nights, home cooking, quiet weekends
- Going out: bars, live events, dancing, karaoke, social sports

Tags must have stable slugs and short labels. New tags should be added through
a seeder/config change, not user input.

## Visibility

Each selected tag has one visibility:

- `public`: visible in discovery, matched profile pages, and shared-interest
  highlights.
- `matches_only`: hidden in discovery, visible after a match.
- `private_matching`: never shown to other users, but usable for ranking.

Discovery can use all three visibility levels for scoring. Discovery UI should
only show shared tags that both users marked `public`.

## Data Model

Add:

- `profile_tag_categories`: `id`, `slug`, `label`, `sort_order`, timestamps.
- `profile_tags`: `id`, `category_id`, `slug`, `label`, `sort_order`, timestamps.
- `dating_profile_tags`: `dating_profile_id`, `profile_tag_id`, `visibility`,
  `sort_order`, timestamps.

Constraints:

- Category slug unique.
- Tag slug unique.
- One pivot row per profile/tag.
- Visibility enum-like values enforced by validation and domain service.

## Profile Editing

The dating profile edit page should show tags grouped by category. Users can
select tags and set visibility per selected tag. MVP should keep this simple:
checkboxes plus a select beside each tag. A reasonable UI limit is 20 selected
tags per profile, with at least 3 recommended but not required.

Saving profile data continues through the CQRS command bus. Controllers must not
validate tags directly.

## Profile Display

Matched profile pages must show:

- primary photo;
- display name, age, city/country;
- relationship intent;
- bio;
- public tags and `matches_only` tags grouped by category.

Discovery cards should show the bio and a short shared-interest line when there
are public shared tags.

## Matching and Ranking

Hard filters remain unchanged: visibility, moderation, active account, blocks,
previous decisions, age, distance, gender/orientation, relationship intent, and
AI Szarnyseged exclusion.

Tags affect only soft ranking in MVP:

- +1 score for each shared tag where both profiles selected it;
- private matching tags count toward score;
- candidates sort by score descending, then `id` ascending for deterministic
  behavior.

Future preference screens can add hard tag filters such as "must like dogs" or
"exclude smoking", but that is outside this MVP slice.

## Seeded Demo Profiles

The demo seeder should assign realistic tag combinations to random/demo
profiles. Seeded tags must use the same taxonomy and visibility rules as real
profiles.

## Tests

Required coverage:

- taxonomy seeder creates categories and tags idempotently;
- users can save profile tags with visibility through the profile edit flow;
- matched profile page shows bio and visible tags;
- `private_matching` tags are not displayed;
- discovery shows shared public tags;
- discovery ranks candidates with more shared tags first.
