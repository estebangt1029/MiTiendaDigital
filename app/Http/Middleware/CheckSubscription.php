<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Subscription;

class CheckSubscription
{
    public function handle(Request $request, Closure $next)
    {
        // Obtener store_id según el guard activo
        if (Auth::guard('owner')->check()) {
            $storeId = session('current_store_id');
        } elseif (Auth::guard('store_user')->check()) {
            $storeId = Auth::guard('store_user')->user()->store_id;
        } else {
            return $next($request);
        }

        if (!$storeId) return $next($request);

        $subscription = Subscription::where('store_id', $storeId)
                                    ->where('active', true)
                                    ->latest()
                                    ->first();

        // Sin suscripción o vencida
        if (!$subscription || now()->gt($subscription->end_date)) {
            $daysExpired = $subscription
                ? now()->diffInDays($subscription->end_date)
                : null;

            return redirect()->route('subscription.expired')
                             ->with('days_expired', $daysExpired)
                             ->with('store_name', session('store_name', 'tu tienda'));
        }

        // Pasar los días restantes a la vista
        $daysLeft = now()->diffInDays($subscription->end_date, false);
        view()->share('subscriptionDaysLeft', $daysLeft);

        return $next($request);
    }
}