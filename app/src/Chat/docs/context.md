# Chat Domain Context

## Purpose

Owns matched conversation display, message sending, and read state. A
conversation exists only after a reciprocal match.

It does not own match creation, discovery decisions, or user account state.

## Core Responsibilities

- show a conversation to a matched participant
- send a text message
- mark messages as read
- list conversations/messages for the match list and chat UI
- broadcast message and read events

## Main Entry Points

### Presentation

- `app/src/Chat/Presentation/Routes/routes.php`
- `ShowConversationController`, `SendMessageController`,
  `ListMessagesController`

### Request Handlers

- `app/src/Chat/RequestHandler/ShowConversation/`
- `app/src/Chat/RequestHandler/SendMessage/`
- `app/src/Chat/RequestHandler/MarkConversationRead/`

### Domain Services

- `ShowConversationService`
- `SendMessageService`
- `MarkConversationReadService`

### Livewire

- `app/app/Livewire/Chat/ConversationPanel.php`
- `app/app/Livewire/Chat/MessageList.php`

## Important Services

### `ShowConversationService`

- Resolves the viewer profile and the conversation.
- Rejects the conversation unless the viewer is a participant.
- Rejects unless viewer and other participant are active, verified, and 18+.
- Returns a conversation view with messages.

### `SendMessageService`

- Persists a message for the conversation.
- Dispatches `MessageSent`.

### `MarkConversationReadService`

- Marks the given messages as read.
- Dispatches `MessagesRead`.

## Business Rules

- Access requires a matched connection; a direct URL is not enough.
- Both participants must have an active account, verified email, and an adult
  birthdate.
- Messages are text-only in the MVP.
- Messages are ordered oldest-to-newest for the chat panel.
- Read state is per conversation and per user.

## Dependencies

Internal:

- `App\Models\Conversation`
- `App\Models\Message`
- `App\Models\Connection`
- `App\Models\DatingProfile`
- `App\Events\MessageSent`
- `App\Events\MessagesRead`

External:

- Laravel Reverb for private-channel broadcasts.

## Data / Ownership

Owns:

- conversations
- messages
- read state

Does not own:

- match eligibility (`Connection` / `Discovery`)
- account status (`User`)
- assistant starter copy (`Assistant`)

## Permissions / Capability Model

- `auth` + `account.active` + `verified`: conversation show/send.
- `auth` + `account.active` + `verified` + `dating.profile`: message list.
- Participant check happens in the domain service, not only in the route.

## Known Constraints

- Text-only messages.
- Reverb must be running for realtime updates; the persisted message still
  exists without it.
- The chat panel listens to private channels.

## Gotchas

- Do not relax the participant check to "any authenticated user".
- Dispatch `MessageSent` after the message is persisted, not before.
- Keep the assistant message (`sender_user_id = null`, `sender_type =
  assistant`) renderable in the same panel.
- Message ordering must stay deterministic (`created_at`, then `id`).

## Related Files

- `app/src/Chat/Presentation/Routes/routes.php`
- `app/src/Chat/DomainService/ShowConversationService.php`
- `app/src/Chat/DomainService/SendMessageService.php`
- `app/src/Chat/DomainService/MarkConversationReadService.php`
- `app/app/Livewire/Chat/ConversationPanel.php`
- `app/app/Models/Conversation.php`
- `app/app/Models/Message.php`
- `docs/architektura.md`

## When To Update This File

Update when:

- conversation access rules change
- message shape or ordering changes
- read-state behavior changes
- a realtime event changes
- attachments or non-text messages are introduced
