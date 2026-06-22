<?php
namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\Product;
use App\Models\InventoryLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    private function currentStore()
    {
        return Store::where('owner_id', Auth::guard('owner')->id())
                    ->findOrFail(session('current_store_id'));
    }

    public function index(Request $request)
    {
        $store      = $this->currentStore();
        $categories = $store->categories()->where('active', true)->get();
        $query      = $store->products()->with('category')->where('active', true);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                  ->orWhere('barcode', 'like', '%'.$request->search.'%');
            });
        }

        $products = $query->orderBy('name')->get();
        $lowStock = $store->products()
                          ->where('active', true)
                          ->whereColumn('stock', '<=', 'min_stock')
                          ->count();

        return view('store.products.index', compact('store', 'products', 'categories', 'lowStock'));
    }

    public function create()
    {
        $store      = $this->currentStore();
        $categories = $store->categories()->where('active', true)->get();
        return view('store.products.create', compact('store', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'cost'        => 'nullable|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'min_stock'   => 'required|integer|min:0',
            'barcode'     => 'nullable|string|max:100',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $store   = $this->currentStore();
        $product = $store->products()->create($request->only(
            'name', 'price', 'cost', 'stock', 'min_stock', 'barcode', 'category_id'
        ));

        InventoryLog::create([
            'store_id'    => $store->id,
            'product_id'  => $product->id,
            'type'        => 'entrada',
            'quantity'    => $product->stock,
            'stock_before'=> 0,
            'stock_after' => $product->stock,
            'note'        => 'Stock inicial',
        ]);

        return redirect()->route('store.products.index')->with('success', 'Producto creado.');
    }

    public function edit(Product $product)
    {
        $store = $this->currentStore();
        abort_if($product->store_id !== $store->id, 403);
        $categories = $store->categories()->where('active', true)->get();
        return view('store.products.edit', compact('store', 'product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $store = $this->currentStore();
        abort_if($product->store_id !== $store->id, 403);

        $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'cost'        => 'nullable|numeric|min:0',
            'min_stock'   => 'required|integer|min:0',
            'barcode'     => 'nullable|string|max:100',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $product->update($request->only('name', 'price', 'cost', 'min_stock', 'barcode', 'category_id'));
        return redirect()->route('store.products.index')->with('success', 'Producto actualizado.');
    }

    public function destroy(Product $product)
    {
        abort_if($product->store_id !== $this->currentStore()->id, 403);
        $product->update(['active' => false]);
        return redirect()->route('store.products.index')->with('success', 'Producto desactivado.');
    }

    public function addStock(Request $request, Product $product)
    {
        $store = $this->currentStore();
        abort_if($product->store_id !== $store->id, 403);

        $request->validate([
            'quantity' => 'required|integer|min:1',
            'note'     => 'nullable|string|max:255',
        ]);

        $before = $product->stock;
        $product->increment('stock', $request->quantity);

        InventoryLog::create([
            'store_id'    => $store->id,
            'product_id'  => $product->id,
            'type'        => 'entrada',
            'quantity'    => $request->quantity,
            'stock_before'=> $before,
            'stock_after' => $product->fresh()->stock,
            'note'        => $request->note ?? 'Entrada de proveedor',
        ]);

        return back()->with('success', "Stock actualizado. Nuevo stock: {$product->fresh()->stock}");
    }

    public function findByBarcode(Request $request)
    {
        $store   = $this->currentStore();
        $product = $store->products()
                         ->where('barcode', $request->barcode)
                         ->where('active', true)
                         ->first();

        if (!$product) {
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }

        return response()->json($product->load('category'));
    }

    public function ajaxCreate(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'barcode' => 'required|string|max:100',
        'price' => 'required|numeric|min:0',
        'cost' => 'nullable|numeric|min:0',
        'stock' => 'required|integer|min:0',
    ]);

    $store = $this->currentStore();

    $existing = $store->products()
        ->where('barcode', $request->barcode)
        ->first();

    if ($existing) {
        return response()->json([
            'error' => 'Ya existe un producto con ese código'
        ], 422);
    }

    $product = $store->products()->create([
        'name' => $request->name,
        'barcode' => $request->barcode,
        'price' => $request->price,
        'cost' => $request->cost ?? 0,
        'stock' => $request->stock,
        'min_stock' => 5,
        'active' => true
    ]);

    InventoryLog::create([
        'store_id' => $store->id,
        'product_id' => $product->id,
        'type' => 'entrada',
        'quantity' => $product->stock,
        'stock_before' => 0,
        'stock_after' => $product->stock,
        'note' => 'Stock inicial'
    ]);

    return response()->json($product);
}
}