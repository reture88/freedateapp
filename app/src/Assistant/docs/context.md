# Assistant Domain Context

## Purpose

Owns the AI wingman starter message for matched conversations. The MVP is
deterministic: it builds Hungarian copy from a small template and visible
profile facts, without an external LLM call.

It does not own conversations, messages, match state, or chat transport.

## Core Responsibilities

- generate a starter message from a safe context
- post one starter to eligible matched conversations
- avoid posting when the conversation already has messages
- broadcast the created message through the existing chat event

## Main Entry Points

### Presentation

- No HTTP route; the flow is invoked by a console command.

### Console

- `app/app/Console/Commands/PostAiWingmanStartersCommand.php`
- Signature: `assistant:post-wingman-starters`

### Domain Services

- `app/src/Assistant/DomainService/AssistantStarterService.php`
- `app/src/Assistant/DomainService/PostAssistantStartersService.php`

### DTO

- `app/src/Assistant/Dto/AssistantStarterContext.php`

## Important Services

### `AssistantStarterService`

Responsibility:

- turn a `AssistantStarterContext` into a message body or `null`.

Input / output:

- input: speaker name, target name, target visible facts, unsafe flag
- output: Hungarian starter text or `null`

Important notes:

- `unsafeContext` short-circuits to `null`.
- Empty facts produce a generic opening question.
- At most two facts are humanized and joined.

### `PostAssistantStartersService`

Responsibility:

- find eligible matched connections and create the starter message.

Input / output:

- input: none
- output: number of posted starters

Important notes:

- Waiting period: 6 hours after `matched_at`.
- Skips connections whose conversation already has messages.
- Requires at least one profile with `ai_wingman_enabled`.
- Creates a `Message` with `sender_user_id = null` and
  `sender_type = 'assistant'`.
- Dispatches `MessageSent`.

## Business Rules

- Only `status = matched` connections with a non-null `matched_at` qualify.
- Only conversations with zero messages qualify.
- The assistant posts once; there is no second starter in the MVP.
- The message is system/assistant content, not a real user message.

## Dependencies

Internal:

- `App\Models\Connection`
- `App\Models\Conversation`
- `App\Models\Message`
- `App\Models\DatingProfile`
- `App\Events\MessageSent`

External:

- None. No external LLM.

## Data / Ownership

Owns:

- assistant starter generation
- assistant message creation

Does not own:

- conversation lifecycle
- match creation
- message transport or read state

## Permissions / Capability Model

- No user-facing route.
- Runs through the console command; production scheduling is an operational
  concern.

## Known Constraints

- Copy is deterministic and Hungarian.
- `unsafeContext` is currently always `false` in the posting service.
- Fact translation is a small `match` list; unknown facts pass through.
- The 6-hour waiting period is a constant.

## Gotchas

- Do not post if `messages()->exists()`; this is the only guard against a
  second starter.
- Do not create a conversation message without dispatching `MessageSent`;
  the Livewire panel relies on it.
- The assistant message must keep `sender_user_id = null`; do not attribute
  it to a real user.

## Related Files

- `app/src/Assistant/DomainService/AssistantStarterService.php`
- `app/src/Assistant/DomainService/PostAssistantStartersService.php`
- `app/src/Assistant/Dto/AssistantStarterContext.php`
- `app/app/Console/Commands/PostAiWingmanStartersCommand.php`
- `app/app/Models/Connection.php`
- `app/app/Models/Message.php`
- `docs/architektura.md`

## When To Update This File

Update when:

- the starter eligibility or waiting period changes
- the copy strategy or language changes
- an external LLM is introduced
- the posting entry point changes
- the assistant message shape changes
