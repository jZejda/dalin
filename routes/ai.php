<?php

declare(strict_types=1);

use App\Mcp\Servers\DalinServer;
use Laravel\Mcp\Facades\Mcp;

/**
 * HTTP transport for the DaLin MCP server.
 *
 *  This endpoint enables remote access to the MCP server via HTTP.
 *  Protected by ApiKeyAuth middleware (x-apikey header).
 *
 *  Configuration in the client's .mcp.json file:
 * {
 *   "mcpServers": {
 *     "dalin": {
 *       "url": "https://vasedomena.cz/mcp/dalin",
 *       "headers": { "x-apikey": "VAS_KLIC" }
 *     }
 *   }
 * }
 */
Mcp::web('/mcp/dalin', DalinServer::class)->middleware('apikey');
