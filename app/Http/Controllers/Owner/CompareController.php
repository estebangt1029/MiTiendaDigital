<?php
namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CompareController extends Controller
{
    private function owner()
    {
        return Auth::guard('owner')->user();
    }

    public function index(Request $request)
    {
        $owner  = $this->owner();
        $period = (int) $request->get('period', 30); // días hacia atrás
        $since  = Carbon::now()->subDays($period);

        // Solo tiendas activas: comparar una tienda inactiva/pendiente no aporta info útil
        $stores = $owner->stores()->where('active', true)->get();

        if ($stores->isEmpty()) {
            return view('owner.compare', [
                'owner'      => $owner,
                'period'     => $period,
                'storeStats' => collect(),
            ]);
        }

        $storeIds = $stores->pluck('id');

        // ── Ventas + recaudo + deuda de clientes por tienda ──────────────
        $salesAgg = Sale::whereIn('store_id', $storeIds)
            ->where('created_at', '>=', $since)
            ->select(
                'store_id',
                DB::raw('COUNT(*) as sales_count'),
                DB::raw('SUM(total) as total_sales'),
                DB::raw('SUM(paid) as total_paid'),
                DB::raw('SUM(debt) as total_pending')
            )
            ->groupBy('store_id')
            ->get()
            ->keyBy('store_id');

        // ── Margen aproximado: subtotal vendido vs costo ACTUAL del producto ──
        // Nota importante: usamos el costo actual de products.cost, no el costo
        // histórico al momento de cada venta (esa info no se guarda por venta).
        // Esto da un margen aproximado, no contable exacto, pero es el dato
        // disponible sin agregar columnas nuevas a sale_items.
        $marginAgg = SaleItem::join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->join('products', 'products.id', '=', 'sale_items.product_id')
            ->whereIn('sales.store_id', $storeIds)
            ->where('sales.created_at', '>=', $since)
            ->select(
                'sales.store_id',
                DB::raw('SUM(sale_items.subtotal) as revenue'),
                DB::raw('SUM(sale_items.quantity * products.cost) as cost_total')
            )
            ->groupBy('sales.store_id')
            ->get()
            ->keyBy('store_id');

        // ── Producto más vendido por tienda (por unidades) ──────────────
        $topProductRows = SaleItem::join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->join('products', 'products.id', '=', 'sale_items.product_id')
            ->whereIn('sales.store_id', $storeIds)
            ->where('sales.created_at', '>=', $since)
            ->select(
                'sales.store_id',
                'products.name',
                DB::raw('SUM(sale_items.quantity) as qty')
            )
            ->groupBy('sales.store_id', 'products.name')
            ->orderByDesc('qty')
            ->get()
            ->groupBy('store_id')
            ->map(fn($rows) => $rows->first()); // el primero de cada grupo ya es el más vendido (orderByDesc)

        // ── Inventario: total productos activos + cuántos en stock bajo ──
        $productAgg = DB::table('products')
            ->whereIn('store_id', $storeIds)
            ->where('active', true)
            ->select(
                'store_id',
                DB::raw('COUNT(*) as total_products'),
                DB::raw('SUM(CASE WHEN stock <= min_stock THEN 1 ELSE 0 END) as low_stock_count')
            )
            ->groupBy('store_id')
            ->get()
            ->keyBy('store_id');

        // ── Clientes: total y cuántos tienen deuda ──────────────────────
        $customerAgg = DB::table('customers')
            ->whereIn('store_id', $storeIds)
            ->where('active', true)
            ->select(
                'store_id',
                DB::raw('COUNT(*) as total_customers'),
                DB::raw('SUM(total_debt) as total_customer_debt')
            )
            ->groupBy('store_id')
            ->get()
            ->keyBy('store_id');

        // ── Proveedores: deuda total que la tienda les debe ─────────────
        $supplierAgg = DB::table('suppliers')
            ->whereIn('store_id', $storeIds)
            ->where('active', true)
            ->select('store_id', DB::raw('SUM(total_debt) as total_supplier_debt'))
            ->groupBy('store_id')
            ->get()
            ->keyBy('store_id');

        // ── Armar un objeto de stats por tienda, todo en un solo lugar ──
        $storeStats = $stores->map(function ($store) use (
            $salesAgg, $marginAgg, $topProductRows, $productAgg, $customerAgg, $supplierAgg
        ) {
            $sales    = $salesAgg->get($store->id);
            $margin   = $marginAgg->get($store->id);
            $products = $productAgg->get($store->id);
            $customers= $customerAgg->get($store->id);
            $suppliers= $supplierAgg->get($store->id);
            $topProd  = $topProductRows->get($store->id);

            $revenue    = $margin->revenue ?? 0;
            $costTotal  = $margin->cost_total ?? 0;
            $profit     = $revenue - $costTotal;
            $marginPct  = $revenue > 0 ? round(($profit / $revenue) * 100, 1) : 0;

            return (object) [
                'id'                  => $store->id,
                'name'                => $store->name,
                'sales_count'         => $sales->sales_count ?? 0,
                'total_sales'         => (float) ($sales->total_sales ?? 0),
                'total_paid'          => (float) ($sales->total_paid ?? 0),
                'total_pending'       => (float) ($sales->total_pending ?? 0),
                'estimated_profit'    => (float) $profit,
                'margin_pct'          => $marginPct,
                'top_product_name'    => $topProd->name ?? null,
                'top_product_qty'     => $topProd->qty ?? 0,
                'total_products'      => $products->total_products ?? 0,
                'low_stock_count'     => $products->low_stock_count ?? 0,
                'total_customers'     => $customers->total_customers ?? 0,
                'total_customer_debt' => (float) ($customers->total_customer_debt ?? 0),
                'total_supplier_debt' => (float) ($suppliers->total_supplier_debt ?? 0),
                'avg_ticket'          => ($sales->sales_count ?? 0) > 0
                                            ? round(($sales->total_sales ?? 0) / $sales->sales_count, 0)
                                            : 0,
            ];
        })->sortByDesc('total_sales')->values();

        // Ranking simple: 1 = mejor tienda por ventas totales del período
        $bestStoreId  = $storeStats->first()->id  ?? null;
        $worstStoreId = $storeStats->last()->id   ?? null;

        return view('owner.compare', compact('owner', 'period', 'storeStats', 'bestStoreId', 'worstStoreId'));
    }
}