---
name: technical-writer
description: 'Use for stakeholder-facing documentation, release notes, product updates, or summaries. Output Hungarian, simple language, business value, no developer jargon. Technical repo docs stay English.'
---

# Technical Writer (Hungarian Stakeholder Docs)

Use this skill when the audience is a stakeholder, product owner, or end user,
not a developer. Technical repo docs (`docs/`, issues, pull requests, commits)
remain English; stakeholder summaries are Hungarian.

## Rules

- Write in Hungarian.
- Use simple, clear language.
- Avoid technical jargon; explain any unavoidable term in one sentence.
- Focus on business value and user impact.
- Keep it concise.
- Use Markdown.
- Do not include internal class names, namespaces, branch names, or raw stack
  lists unless they are explained.

## Structure

1. **Cím** — clear and descriptive.
2. **Áttekintés** — 2–3 sentences.
3. **Főbb jellemzők** — bullet points.
4. **Előnyök** — business impact.
5. **Működés** — how it works for the user, if relevant.
6. **Következő lépések** — if applicable.

## Release notes format

```markdown
### vX.Y.Z (ÉÉÉÉ-HH-NN)

- **Új funkciók**
  - ...
- **Javítások**
  - ...
- **Felhasználói teendő**
  - ...
- **Ismert korlátozások**
  - ...
```

## Tone

- Professional, approachable, actionable, positive.
- Prefer short paragraphs (2–3 sentences).
- Use tables for comparisons.
- Use bold for key terms.

## MTAM-specific language

- Use the product name MTAM or Freedateapp consistently in a document.
- Use the established product terms: dating profile, match, conversation,
  discovery, AI Szárnysegéd, account status.
- Do not describe MTAM as a webshop; it is a dating app.
- Keep legal copy honest: if something still needs legal review, say so.

## When to use

- Stakeholder update after an epic.
- Release notes for a version.
- Product-facing explanation of a new flow.
- Summary of a technical change for a non-technical audience.

## Related

- `docs/freedateapp-product-scope.md`
- `docs/freedateapp-technical-spec.md`
- `docs/freedateapp-figma-design-brief.md`
