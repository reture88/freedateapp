# Chat Domain Instructions

## Required Context

Before planning or implementing changes in this domain, read:

- `AGENTS.md` at the repo root
- `docs/architektura.md`
- `docs/freedateapp-technical-spec.md`
- `app/src/Chat/docs/context.md`

## Working Rules

- Only matched participants may read or write a conversation.
- Keep message transport, read state, and access checks inside this domain.
- Dispatch the existing chat events (`MessageSent`, `MessagesRead`) after
  persistence; the Livewire panel relies on them.
- Keep chat text-only in the MVP; do not add attachments without a product
  decision.

## Update Trigger

Update `app/src/Chat/docs/context.md` when conversation access, message
shape, ordering, read state, or realtime events change.
