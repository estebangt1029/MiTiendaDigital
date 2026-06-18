<?php
namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $store = Auth::guard('store_user')->user()->store;

        $totalHoy    = $store->sales()->whereDate('created_at', today())->sum('total');
        $ventasHoy   = $store->sales()->whereDate('created_at', today())->count();
        $pendientes  = $store->sales()->where('status', '!=', 'pagada')->count();
        $totalDeuda  = $store->customers()->where('active', true)->sum('total_debt');
        $lowStock    = $store->products()->where('active', true)->whereColumn('stock', '<=', 'min_stock')->count();

        $salesByDay = $store->sales()
            ->where('created_at', '>=', now()->subDays(7))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')->orderBy('date')->get();

        return view('employee.reports.index', compact('store', 'totalHoy', 'ventasHoy', 'pendientes', 'totalDeuda', 'lowStock', 'salesByDay'));
    }
}