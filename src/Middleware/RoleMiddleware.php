<?php

namespace Taibi\AuthAPI\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Non authentifié.',
                'code' => 'UNAUTHENTICATED',
            ], 401);
        }

        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        return response()->json([
            'message' => 'Accès non autorisé. Rôle requis : ' . implode(', ', $roles),
            'code' => 'FORBIDDEN_ROLE',
            'current_role' => $user->role,
        ], 403);
    }
}