<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckStoreUserRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::guard('store_user')->user();

        if (!$user || !in_array($user->role, $roles)) {
            abort(403, 'No tienes permisos para realizar esta acción.');
        }

        return $next($request);
    }
}