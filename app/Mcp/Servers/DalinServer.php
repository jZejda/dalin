<?php

declare(strict_types=1);

namespace App\Mcp\Servers;

use App\Mcp\Tools\CreditBalanceTool;
use App\Mcp\Tools\GetPageTool;
use App\Mcp\Tools\GetPostTool;
use App\Mcp\Tools\PagesTool;
use App\Mcp\Tools\PostsTool;
use App\Mcp\Tools\RaceProfilesTool;
use App\Mcp\Tools\SportEventsTool;
use App\Mcp\Tools\UserEntriesTool;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;

#[Name('DaLin')]
#[Version('1.0.0')]
#[Instructions(
    <<<'MARKDOWN'
    Tento MCP server poskytuje přístup k datům orientačního klubu DaLin.
    Umožňuje dotazovat se na závody, přihlášky, závodní profily, kredit uživatelů,
    články a stránky.

    Nástroje pro uživatelská data (race_profiles, user_entries, credit_balance)
    vyžadují parametr user_id pokud není autentizovaný uživatel k dispozici přes HTTP.
    MARKDOWN
)]
class DalinServer extends Server
{
    /** @var array<int, class-string<\Laravel\Mcp\Server\Tool>> */
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
