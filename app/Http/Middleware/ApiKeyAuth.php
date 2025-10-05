<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('x-apikey');

        if ($apiKey === null || $apiKey === '') {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Use sha256 hex hash for constant-time lookup
        $apiKeyHash = hash('sha256', $apiKey);

        /** @var User|null $user */
        $user = User::query()
            ->where('api_key_hash', '=', $apiKeyHash)
            ->first();

        if ($user === null || !$user->isActive()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Set the authenticated user for the current request context
        auth()->setUser($user);

        return $next($request);
    }
}


