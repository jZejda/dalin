<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Models\User;
use App\Models\UserCredit;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\DB;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Description('Vrátí aktuální zůstatek kreditu uživatele v CZK.')]
#[IsReadOnly]
class CreditBalanceTool extends Tool
{
    public function handle(Request $request): Response
    {
        $user = $this->resolveUser($request);

        if ($user === null) {
            return Response::error('Parametr user_id je povinný nebo použijte HTTP transport s x-apikey autentizací.');
        }

        $balance = DB::table('user_credits')
            ->where('user_id', '=', $user->id)
            ->sum('amount');

        return Response::json([
            'user_id'  => $user->id,
            'amount'   => (float) $balance,
            'currency' => UserCredit::CURRENCY_CZK,
        ]);
    }

    /** @return array<string, mixed> */
    public function schema(JsonSchema $schema): array
    {
        return [
            'user_id' => $schema->integer()->description('ID uživatele. Vyžadováno při stdio transportu.'),
        ];
    }

    private function resolveUser(Request $request): ?User
    {
        $authUser = auth()->user();
        if ($authUser instanceof User) {
            return $authUser;
        }

        $userId = $request->get('user_id');
        if ($userId === null) {
            return null;
        }

        $found = User::find((int) $userId);

        return $found instanceof User ? $found : null;
    }
}
