<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Models\Store;
use App\Models\Sale;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Stats generales
        $stats = [
            'total_owners'   => Owner::count(),
            'active_stores'  => Store::where('active', true)->count(),
            'inactive_stores'=> Store::where('active', false)->count(),
            'total_stores'   => Store::count(),
            'pending_subs'   => Subscription::where('status', 'pending')->count(),
            'active_subs'    => Subscription::where('status', 'active')
                                    ->where('end_date', '>=', now())->count(),
            'expiring_soon'  => Subscription::where('status', 'active')
                                    ->where('end_date', '<=', now()->addDays(7))
                                    ->where('end_date', '>=', now())->count(),
            'expired'        => Subscription::where('status', 'active')
                                    ->where('end_date', '<', now())->count(),
            // Ingresos — suma de suscripciones activas confirmadas
            'total_revenue'  => Subscription::where('status', 'active')->sum('price'),
            'monthly_revenue'=> Subscription::where('status', 'active')
                                    ->where('confirmed_at', '>=', now()->startOfMonth())
                                    ->sum('price'),
            // Ventas totales en el sistema
            'total_sales'    => Sale::sum('total'),
            'sales_today'    => Sale::whereDate('created_at', today())->sum('total'),
        ];

        // Suscripciones pendientes de confirmar
        $pendingSubs = Subscription::with(['owner', 'store'])
                            ->where('status', 'pending')
                            ->latest()->get();

        // Suscripciones por vencer
        $expiringSoon = Subscription::with(['owner', 'store'])
                            ->where('status', 'active')
                            ->where('end_date', '<=', now()->addDays(7))
                            ->where('end_date', '>=', now())
                            ->orderBy('end_date')->get();

        // Ventas por tienda (top 5)
        $topStores = Store::withSum('sales', 'total')
                          ->withCount('sales')
                          ->where('active', true)
                          ->orderByDesc('sales_sum_total')
                          ->limit(5)
                          ->get();

        // Ingresos por mes (últimos 6 meses)
        $revenueByMonth = Subscription::where('status', 'active')
                            ->whereNotNull('confirmed_at')
                            ->where('confirmed_at', '>=', now()->subMonths(6))
                            ->select(
                                DB::raw("DATE_FORMAT(confirmed_at, '%Y-%m') as month"),
                                DB::raw('SUM(price) as total')
                            )
                            ->groupBy('month')
                            ->orderBy('month')
                            ->get();

        return view('admin.dashboard', compact(
            'stats', 'pendingSubs', 'expiringSoon', 'topStores', 'revenueByMonth'
        ));
    }
}