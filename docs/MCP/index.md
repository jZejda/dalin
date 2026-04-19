# DaLin MCP Server

The DaLin MCP (Model Context Protocol) server exposes orienteering club data to AI assistants and other MCP-compatible clients. It is built on top of [Laravel MCP](https://github.com/laravel/mcp) and ships as part of the DaLin application.

**Server name:** `DaLin`  
**Version:** `1.0.0`  
**Transport options:** stdio (local), HTTP (remote)

## Quick navigation

- [Authentication & transports](authentication.md)
- [Configuration examples](configuration.md)
- [Tools reference](tools/index.md)

## Available tools

| Tool | Category | Auth required |
|------|----------|---------------|
| [`sport_events`](tools/sport-events.md) | Events | No |
| [`user_entries`](tools/user-entries.md) | User data | Yes |
| [`race_profiles`](tools/race-profiles.md) | User data | Yes (HTTP only) |
| [`credit_balance`](tools/credit-balance.md) | User data | Yes (HTTP only) |
| [`posts`](tools/posts.md) | Content | No |
| [`get_post`](tools/posts.md#get_post) | Content | No |
| [`pages`](tools/pages.md) | Content | No |
| [`get_page`](tools/pages.md#get_page) | Content | No |

## Adding a new tool

1. Create a new class in `app/Mcp/Tools/` extending `Laravel\Mcp\Server\Tool`.
2. Add `#[Description('...')]` and optionally `#[IsReadOnly]` attributes.
3. Implement `handle(Request $request): Response` and `schema(JsonSchema $schema): array`.
4. Register the class in `$tools` array inside `app/Mcp/Servers/DalinServer.php`.
5. Add a documentation page in `docs/MCP/tools/` and update the table above.
