<?php
namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\Purchase;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\InventoryLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    private function currentStore()
    {
        return Store::where('owner_id', Auth::guard('owner')->id())
                    ->findOrFail(session('current_store_id'));
    }

    public function index(Request $request)
    {
        $store = $this->currentStore();
        $query = $store->purchases()->with(['supplier', 'storeUser'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $purchases  = $query->get();
        $totalMes   = $store->purchases()->whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year)->sum('total');
        $pendientes = $store->purchases()->where('status', '!=', 'pagada')->count();

        return view('store.purchases.index', compact('store', 'purchases', 'totalMes', 'pendientes'));
    }

    public function create()
    {
        $store     = $this->currentStore();
        $suppliers = $store->suppliers()->where('active', true)->orderBy('name')->get();
        $products  = $store->products()->where('active', true)->with('category')->orderBy('name')->get();
        return view('store.purchases.create', compact('store', 'suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type'                => 'required|in:contado,credito',
            'items'               => 'required|array|min:1',
            'items.*.product_id'  => 'required|exists:products,id',
            'items.*.quantity'    => 'required|integer|min:1',
            'items.*.unit_cost'   => 'required|numeric|min:0',
            'paid'                => 'required|numeric|min:0',
            'supplier_id'         => 'nullable|exists:suppliers,id',
            'update_product_cost' => 'nullable|boolean',
        ]);

        // Crédito sin proveedor no tiene a quién cobrarle la deuda
        if ($request->type === 'credito' && !$request->supplier_id) {
            return redirect()->back()->withInput()
                ->withErrors(['supplier_id' => 'Para compras a crédito debes seleccionar un proveedor.']);
        }

        $store = $this->currentStore();

        DB::transaction(function () use ($request, $store) {
            $total     = 0;
            $itemsData = [];

            // lockForUpdate evita que dos compras simultáneas lean el mismo
            // stock viejo y se pisen al actualizar (mismo fix aplicado en ventas)
            foreach ($request->items as $item) {
                $product = Product::where('store_id', $store->id)
                                  ->lockForUpdate()
                                  ->findOrFail($item['product_id']);

                $subtotal = $item['unit_cost'] * $item['quantity'];
                $total   += $subtotal;

                $itemsData[] = [
                    'product'   => $product,
                    'quantity'  => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'subtotal'  => $subtotal,
                ];
            }

            $paid   = min($request->paid, $total);
            $debt   = $total - $paid;
            $status = $debt <= 0 ? 'pagada' : ($paid > 0 ? 'parcial' : 'pendiente');

            $purchase = $store->purchases()->create([
                'supplier_id'          => $request->supplier_id,
                'store_user_id'        => null,
                'type'                 => $request->type,
                'total'                => $total,
                'paid'                 => $paid,
                'debt'                 => $debt,
                'status'               => $status,
                'update_product_cost'  => $request->boolean('update_product_cost', true),
                'notes'                => $request->notes,
            ]);

            foreach ($itemsData as $item) {
                $purchase->items()->create([
                    'product_id' => $item['product']->id,
                    'quantity'   => $item['quantity'],
                    'unit_cost'  => $item['unit_cost'],
                    'subtotal'   => $item['subtotal'],
                ]);

                $before = $item['product']->stock;
                $item['product']->increment('stock', $item['quantity']);

                // Actualizar el costo del producto si la compra lo pide
                if ($request->boolean('update_product_cost', true)) {
                    $item['product']->update(['cost' => $item['unit_cost']]);
                }

                InventoryLog::create([
                    'store_id'     => $store->id,
                    'product_id'   => $item['product']->id,
                    'type'         => 'compra',
                    'quantity'     => $item['quantity'],
                    'stock_before' => $before,
                    'stock_after'  => $before + $item['quantity'],
                    'note'         => "Compra #{$purchase->id}",
                ]);
            }

            // Igual que con clientes/fiado: si queda deuda, se acumula en el proveedor
            if ($request->supplier_id && $debt > 0) {
                Supplier::find($request->supplier_id)->increment('total_debt', $debt);
            }

            if ($paid > 0 && $request->supplier_id) {
                $purchase->payments()->create([
                    'supplier_id'   => $request->supplier_id,
                    'store_id'      => $store->id,
                    'store_user_id' => null,
                    'amount'        => $paid,
                    'method'        => $request->payment_method ?? 'efectivo',
                ]);
            }
        });

        return redirect()->route('store.purchases.index')->with('success', 'Compra registrada exitosamente.');
    }

    public function show(Purchase $purchase)
    {
        $store = $this->currentStore();
        abort_if($purchase->store_id !== $store->id, 403);
        $purchase->load(['items.product', 'supplier', 'payments']);
        return view('store.purchases.show', compact('store', 'purchase'));
    }
}