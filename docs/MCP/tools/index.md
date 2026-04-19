# Tools Reference

All tools in the DaLin MCP server are **read-only** (annotated with `#[IsReadOnly]`). They never modify application data.

## Tool categories

### Events

| Tool | Class | Description |
|------|-------|-------------|
| [`sport_events`](sport-events.md) | `SportEventsTool` | List sport events (races, trainings) with date / type / category filtering |

### User data

These tools require an authenticated user. With HTTP transport the user is resolved from the API key. With stdio transport only `user_entries` supports a fallback `user_id` parameter; `race_profiles` and `credit_balance` require HTTP transport.

| Tool | Class | Description |
|------|-------|-------------|
| [`race_profiles`](race-profiles.md) | `RaceProfilesTool` | List race profiles for the authenticated user |
| [`user_entries`](user-entries.md) | `UserEntriesTool` | List event entries for the authenticated user |
| [`credit_balance`](credit-balance.md) | `CreditBalanceTool` | Get current credit balance for the authenticated user |

### Content

| Tool | Class | Description |
|------|-------|-------------|
| [`posts`](posts.md) | `PostsTool` | List news articles with date filtering |
| [`get_post`](posts.md#get_post) | `GetPostTool` | Get full detail of a single article |
| [`pages`](pages.md) | `PagesTool` | List CMS pages with date / status / category filtering |
| [`get_page`](pages.md#get_page) | `GetPageTool` | Get full detail of a single page |

## Common conventions

### Pagination

List tools use cursor-based `simplePaginate`. The response always contains:

```json
{
  "data": [...],
  "per_page": 20,
  "next_cursor": "...",
  "next_page_url": "..."
}
```

The `per_page` parameter defaults to `20` and is capped at `100`.

### Date parameters

Date strings must follow `Y-m-d` format (e.g. `2025-06-01`). Internally, `from` is expanded to start-of-day and `to` to end-of-day in application timezone.

### Error responses

When a required parameter is missing or a record is not found, the tool returns an MCP error response (not a JSON body). The error message is in Czech as that is the primary user language.
