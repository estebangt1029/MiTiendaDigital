<?php
namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    private function currentStore()
    {
        return Store::where('owner_id', Auth::guard('owner')->id())
                    ->findOrFail(session('current_store_id'));
    }

    public function index(Request $request)
    {
        $store  = $this->currentStore();
        $period = $request->get('period', '30');
        $from   = now()->subDays((int)$period)->startOfDay();
        $to     = now()->endOfDay();

        // Ventas por día
        $salesByDay = $store->sales()
            ->whereBetween('created_at', [$from, $to])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as total'),
                DB::raw('SUM(paid) as paid'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Productos más vendidos
        $topProducts = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.store_id', $store->id)
            ->whereBetween('sales.created_at', [$from, $to])
            ->select(
                'products.name',
                DB::raw('SUM(sale_items.quantity) as total_qty'),
                DB::raw('SUM(sale_items.subtotal) as total_revenue')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_qty')
            ->limit(8)
            ->get();

        // Ventas por categoría
        $byCategory = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->where('sales.store_id', $store->id)
            ->whereBetween('sales.created_at', [$from, $to])
            ->select(
                DB::raw('COALESCE(categories.name, "Sin categoría") as category'),
                DB::raw('SUM(sale_items.subtotal) as total')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total')
            ->get();

        // Resumen general
        $summary = [
            'total_sales'    => $store->sales()->whereBetween('created_at', [$from, $to])->sum('total'),
            'total_paid'     => $store->sales()->whereBetween('created_at', [$from, $to])->sum('paid'),
            'total_debt'     => $store->sales()->whereBetween('created_at', [$from, $to])->sum('debt'),
            'count_sales'    => $store->sales()->whereBetween('created_at', [$from, $to])->count(),
            'count_fiado'    => $store->sales()->whereBetween('created_at', [$from, $to])->where('type', 'fiado')->count(),
            'pending_debt'   => $store->customers()->where('active', true)->sum('total_debt'),
            'low_stock'      => $store->products()->where('active', true)->whereColumn('stock', '<=', 'min_stock')->count(),
            'total_products' => $store->products()->where('active', true)->count(),
            'total_customers'=> $store->customers()->where('active', true)->count(),
        ];

        // Clientes con más deuda
        $topDebtors = $store->customers()
            ->where('active', true)
            ->where('total_debt', '>', 0)
            ->orderByDesc('total_debt')
            ->limit(5)
            ->get();

        return view('store.reports.index', compact(
            'store', 'period', 'summary',
            'salesByDay', 'topProducts', 'byCategory', 'topDebtors'
        ));
    }
}