///<?php

use App\Mcp\Servers\DalinServer;
use Laravel\Mcp\Facades\Mcp;

/**
 * HTTP transport pro MCP server DaLin.
 *
 * Tento endpoint umožňuje vzdálený přístup k MCP serveru přes HTTP.
 * Chráněno middleware ApiKeyAuth (x-apikey hlavička).
 *
 * Konfigurace v .mcp.json klienta:
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
