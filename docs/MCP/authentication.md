# Authentication & Transports

The DaLin MCP server supports two transport modes with different authentication mechanisms.

## stdio transport (local)

Used when the MCP client runs on the same machine as the application (e.g. Claude Code, Cursor).  
The server is started as a subprocess via Laravel Sail:

```json
{
  "mcpServers": {
    "dalin": {
      "command": "vendor/bin/sail",
      "args": ["artisan", "mcp:start", "dalin"]
    }
  }
}
```

**No HTTP authentication is required.** However, there is no authenticated user context in this mode — tools that depend on an authenticated user (e.g. `race_profiles`, `credit_balance`) are not usable unless the tool also accepts a `user_id` parameter.

See [Tools reference](tools/index.md) for per-tool authentication requirements.

## HTTP transport (remote)

The server is also exposed as an HTTP endpoint at `/mcp/dalin`, protected by the `ApiKeyAuth` middleware.  
The client must send its API key in the `x-apikey` request header.

```json
{
  "mcpServers": {
    "dalin": {
      "url": "https://yourdomain.cz/mcp/dalin",
      "headers": {
        "x-apikey": "YOUR_KEY"
      }
    }
  }
}
```

API keys are managed in the DaLin admin panel. When a valid key is provided, the user associated with that key is set as the authenticated user, which unlocks all user-scoped tools.

### Route definition

```php
// routes/ai.php
Mcp::web('/mcp/dalin', DalinServer::class)->middleware('apikey');
```

### Middleware

`App\Http\Middleware\ApiKeyAuth` validates the `x-apikey` header and logs in the corresponding user for the duration of the request.
