<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthApiToken
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->is('api/login')) {
            return $next($request);
        }

        $authHeader = $request->header('Authorization');

        if (!$authHeader || !preg_match('/^Bearer\s+(.+)$/', $authHeader, $matches)) {
            return response()->json(['message' => 'Unauthorized, token tidak ditemukan'], 401);
        }

        $plainToken = $matches[1];

        $user = User::where('remember_token', $plainToken)->first();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized, token tidak cocok'], 401);
        }

        // Set user sebagai pengguna aktif di sistem Laravel
        Auth::login($user);

        return $next($request);
    }
}
