# Configuration

MCP clients read server definitions from a `.mcp.json` file. DaLin ships with a project-level `.mcp.json` at the repository root.

## Local development (stdio)

Both servers (`dalin` and `laravel-boost`) are started via Laravel Sail:

```json
{
  "mcpServers": {
    "dalin": {
      "command": "vendor/bin/sail",
      "args": ["artisan", "mcp:start", "dalin"]
    },
    "laravel-boost": {
      "command": "vendor/bin/sail",
      "args": ["artisan", "boost:mcp"]
    }
  }
}
```

## Production (HTTP)

For remote access, replace the `command`/`args` pair with a `url` and the appropriate API key header:

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

## Server registration

The `DalinServer` is the only custom MCP server in DaLin. Tools are registered inside `app/Mcp/Servers/DalinServer.php`:

```php
#[Name('DaLin')]
#[Version('1.0.0')]
class DalinServer extends Server
{
    protected array $tools = [
        RaceProfilesTool::class,
        UserEntriesTool::class,
        CreditBalanceTool::class,
        SportEventsTool::class,
        PostsTool::class,
        GetPostTool::class,
        PagesTool::class,
        GetPageTool::class,
    ];
}
```
