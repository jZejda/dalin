<?php

declare(strict_types=1);

use App\Enums\UserCreditStatus;
use App\Enums\UserCreditType;
use App\Mcp\Servers\DalinServer;
use App\Mcp\Tools\CreditBalanceTool;
use App\Mcp\Tools\GetPageTool;
use App\Mcp\Tools\GetPostTool;
use App\Mcp\Tools\PagesTool;
use App\Mcp\Tools\PostsTool;
use App\Mcp\Tools\RaceProfilesTool;
use App\Mcp\Tools\SportEventsTool;
use App\Mcp\Tools\UserEntriesTool;
use App\Models\SportEvent;
use App\Models\User;
use App\Models\UserCredit;

const MCP_PROTOCOL_VERSION = '2026-07-28';

// Far-future window so leftover events in the testing DB don't push ours off the first page
const MCP_EVENT_DATE = '2099-06-15';
const MCP_EVENT_FILTER = ['from' => '2099-06-01', 'to' => '2099-06-30'];

function mcpApiUser(): User
{
    $user = User::factory()->create(['active' => true]);
    $user->setApiKey('test-mcp-key-' . $user->id);

    return $user;
}

/**
 * @param  array<string, mixed>  $params
 * @return array<string, mixed>
 */
function mcpModernMessage(string $method, array $params = []): array
{
    return [
        'jsonrpc' => '2.0',
        'id' => 1,
        'method' => $method,
        'params' => [
            ...$params,
            '_meta' => [
                'io.modelcontextprotocol/protocolVersion' => MCP_PROTOCOL_VERSION,
                'io.modelcontextprotocol/clientCapabilities' => [],
            ],
        ],
    ];
}

it('registers all DaLin tools', function (): void {
    DalinServer::tools()->assertRegistered([
        RaceProfilesTool::class,
        UserEntriesTool::class,
        CreditBalanceTool::class,
        SportEventsTool::class,
        PostsTool::class,
        GetPostTool::class,
        PagesTool::class,
        GetPageTool::class,
    ]);
});

it('lists sport events', function (): void {
    SportEvent::factory()->create(['name' => 'Jihomoravský žebříček', 'date' => MCP_EVENT_DATE]);

    DalinServer::tool(SportEventsTool::class, MCP_EVENT_FILTER)
        ->assertOk()
        ->assertSee('Jihomoravský žebříček');
});

it('returns the credit balance of the authenticated user', function (): void {
    $user = User::factory()->create();

    UserCredit::create([
        'user_id' => $user->id,
        'amount' => 350.0,
        'currency' => UserCredit::CURRENCY_CZK,
        'source' => UserCredit::SOURCE_USER,
        'status' => UserCreditStatus::Open,
        'credit_type' => UserCreditType::InitialDeposit,
    ]);

    DalinServer::actingAs($user)
        ->tool(CreditBalanceTool::class)
        ->assertOk()
        ->assertSee('"amount":350');
});

it('refuses the credit balance without an authenticated user', function (): void {
    DalinServer::tool(CreditBalanceTool::class)->assertHasErrors();
});

it('rejects HTTP requests without an API key', function (): void {
    $this->postJson('/mcp/dalin', mcpModernMessage('tools/list'), [
        'MCP-Protocol-Version' => MCP_PROTOCOL_VERSION,
        'Mcp-Method' => 'tools/list',
    ])->assertUnauthorized();
});

it('serves legacy initialize clients over HTTP', function (): void {
    $user = mcpApiUser();

    $this->withHeader('x-apikey', (string) $user->api_key_hash)
        ->postJson('/mcp/dalin', [
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'initialize',
            'params' => [
                'protocolVersion' => '2025-06-18',
                'capabilities' => [],
                'clientInfo' => ['name' => 'test-client', 'version' => '1.0.0'],
            ],
        ])
        ->assertOk()
        ->assertJsonPath('result.protocolVersion', '2025-06-18')
        ->assertJsonPath('result.serverInfo.name', 'DaLin');
});

it('calls a tool over HTTP with the 2026-07-28 protocol', function (): void {
    $user = mcpApiUser();
    SportEvent::factory()->create(['name' => 'Mistrovství oblasti na krátké trati', 'date' => MCP_EVENT_DATE]);

    $this->withHeader('x-apikey', (string) $user->api_key_hash)
        ->postJson('/mcp/dalin', mcpModernMessage('tools/call', [
            'name' => 'sport-events-tool',
            'arguments' => MCP_EVENT_FILTER,
        ]), [
            'MCP-Protocol-Version' => MCP_PROTOCOL_VERSION,
            'Mcp-Method' => 'tools/call',
            'Mcp-Name' => 'sport-events-tool',
        ])
        ->assertOk()
        ->assertJsonPath('result.isError', false)
        ->assertSee('Mistrovství oblasti na krátké trati');
});
