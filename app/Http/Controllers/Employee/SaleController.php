<?php
namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Customer;
use App\Models\InventoryLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    private function currentStore()
    {
        return Auth::guard('store_user')->user()->store;
    }

    public function index(Request $request)
    {
        $store      = $this->currentStore();
        $sales      = $store->sales()->with(['customer'])->latest()->get();
        $totalHoy   = $store->sales()->whereDate('created_at', today())->sum('total');
        $pendientes = $store->sales()->where('status', '!=', 'pagada')->count();
        return view('employee.sales.index', compact('store', 'sales', 'totalHoy', 'pendientes'));
    }

    public function create()
    {
        $store     = $this->currentStore();
        $customers = $store->customers()->where('active', true)->orderBy('name')->get();
        $products  = $store->products()->where('active', true)->where('stock', '>', 0)
                           ->with('category')->orderBy('name')->get();
        return view('employee.sales.create', compact('store', 'customers', 'products'));
    }

    public function save(Request $request)
    {
        $request->validate([
            'type'               => 'required|in:contado,fiado',
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'paid'               => 'required|numeric|min:0',
            'customer_id'        => 'nullable|exists:customers,id',
        ]);

        $store     = $this->currentStore();
        $storeUser = Auth::guard('store_user')->user();

        foreach ($request->items as $item) {
            $product = Product::where('store_id', $store->id)->findOrFail($item['product_id']);
            if ($product->stock < $item['quantity']) {
                return redirect()->back()->withInput()
                    ->withErrors(['stock' => "Stock insuficiente para \"{$product->name}\". Disponible: {$product->stock}"]);
            }
        }

        DB::transaction(function () use ($request, $store, $storeUser) {
            $total = 0;
            $itemsData = [];

            foreach ($request->items as $item) {
                $product  = Product::where('store_id', $store->id)->findOrFail($item['product_id']);
                $subtotal = $product->price * $item['quantity'];
                $total   += $subtotal;
                $itemsData[] = [
                    'product'  => $product,
                    'quantity' => $item['quantity'],
                    'price'    => $product->price,
                    'subtotal' => $subtotal,
                ];
            }

            $paid   = min($request->paid, $total);
            $debt   = $total - $paid;
            $status = $debt <= 0 ? 'pagada' : ($paid > 0 ? 'parcial' : 'pendiente');

            $sale = $store->sales()->create([
                'customer_id'   => $request->customer_id,
                'store_user_id' => $storeUser->id,
                'type'          => $request->type,
                'total'         => $total,
                'paid'          => $paid,
                'debt'          => $debt,
                'status'        => $status,
                'notes'         => $request->notes,
            ]);

            foreach ($itemsData as $item) {
                $sale->items()->create([
                    'product_id' => $item['product']->id,
                    'quantity'   => $item['quantity'],
                    'unit_price' => $item['price'],
                    'subtotal'   => $item['subtotal'],
                ]);
                $before = $item['product']->stock;
                $item['product']->decrement('stock', $item['quantity']);
                InventoryLog::create([
                    'store_id'      => $store->id,
                    'product_id'    => $item['product']->id,
                    'store_user_id' => $storeUser->id,
                    'type'          => 'venta',
                    'quantity'      => $item['quantity'],
                    'stock_before'  => $before,
                    'stock_after'   => $before - $item['quantity'],
                    'note'          => "Venta #{$sale->id}",
                ]);
            }

            if ($request->customer_id && $debt > 0) {
                Customer::find($request->customer_id)->increment('total_debt', $debt);
            }
        });

        return redirect()->route('employee.sales.index')->with('success', 'Venta registrada.');
    }

    public function show(Sale $sale)
    {
        abort_if($sale->store_id !== $this->currentStore()->id, 403);
        $sale->load(['items.product', 'customer', 'payments']);
        return view('employee.sales.show', compact('sale'));
    }
}