<?php
namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Store;
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
        return Store::where('owner_id', Auth::guard('owner')->id())
                    ->findOrFail(session('current_store_id'));
    }

    public function index(Request $request)
    {
        $store = $this->currentStore();
        $query = $store->sales()->with(['customer', 'storeUser'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $sales       = $query->get();
        $totalHoy    = $store->sales()->whereDate('created_at', today())->sum('total');
        $pendientes  = $store->sales()->where('status', '!=', 'pagada')->count();

        return view('store.sales.index', compact('store', 'sales', 'totalHoy', 'pendientes'));
    }

    public function create()
    {
        $store     = $this->currentStore();
        $customers = $store->customers()->where('active', true)->orderBy('name')->get();
        $products  = $store->products()->where('active', true)->where('stock', '>', 0)
                           ->with('category')->orderBy('name')->get();
        return view('store.sales.create', compact('store', 'customers', 'products'));
    }

    public function store(Request $request)
{
    $request->validate([
        'type'               => 'required|in:contado,fiado',
        'items'              => 'required|array|min:1',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.quantity'   => 'required|integer|min:1',
        'paid'               => 'required|numeric|min:0',
        'customer_id'        => 'nullable|exists:customers,id',
    ]);

    $store = $this->currentStore();

    // Primero verificar stock antes de abrir la transacción
    foreach ($request->items as $item) {
        $product = Product::where('store_id', $store->id)->findOrFail($item['product_id']);
        if ($product->stock < $item['quantity']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['stock' => "Stock insuficiente para \"{$product->name}\". Disponible: {$product->stock}"]);
        }
    }

    DB::transaction(function () use ($request, $store) {
        $total     = 0;
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
            'store_user_id' => null,
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
                'store_id'    => $store->id,
                'product_id'  => $item['product']->id,
                'type'        => 'venta',
                'quantity'    => $item['quantity'],
                'stock_before'=> $before,
                'stock_after' => $before - $item['quantity'],
                'note'        => "Venta #{$sale->id}",
            ]);
        }

        if ($request->customer_id && $debt > 0) {
            Customer::find($request->customer_id)->increment('total_debt', $debt);
        }

        if ($paid > 0 && $request->customer_id) {
            $sale->payments()->create([
                'customer_id'   => $request->customer_id,
                'store_id'      => $store->id,
                'store_user_id' => null,
                'amount'        => $paid,
                'method'        => $request->payment_method ?? 'efectivo',
            ]);
        }
    });

    return redirect()->route('store.sales.index')->with('success', 'Venta registrada exitosamente.');
}

    public function show(Sale $sale)
    {
        $store = $this->currentStore();
        abort_if($sale->store_id !== $store->id, 403);
        $sale->load(['items.product', 'customer', 'payments']);
        return view('store.sales.show', compact('store', 'sale'));
    }
}