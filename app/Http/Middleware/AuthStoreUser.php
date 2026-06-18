<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthStoreUser
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('store_user')->check()) {
            return redirect()->route('storeuser.login');
        }
        return $next($request);
    }
}