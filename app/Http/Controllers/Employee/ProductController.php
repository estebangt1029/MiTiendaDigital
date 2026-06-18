<?php
namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\InventoryLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    private function store()
    {
        return Auth::guard('store_user')->user()->store;
    }

    public function index(Request $request)
    {
        $store    = $this->store();
        $query    = $store->products()->with('category')->where('active', true);
        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }
        $products = $query->orderBy('name')->get();
        $lowStock = $store->products()->where('active', true)->whereColumn('stock', '<=', 'min_stock')->count();
        $categories = $store->categories()->where('active', true)->get();
        return view('employee.products.index', compact('store', 'products', 'lowStock', 'categories'));
    }

    public function addStock(Request $request, Product $product)
    {
        abort_if($product->store_id !== $this->store()->id, 403);
        $request->validate(['quantity' => 'required|integer|min:1', 'note' => 'nullable|string']);

        $before = $product->stock;
        $product->increment('stock', $request->quantity);

        InventoryLog::create([
            'store_id'      => $this->store()->id,
            'product_id'    => $product->id,
            'store_user_id' => Auth::guard('store_user')->id(),
            'type'          => 'entrada',
            'quantity'      => $request->quantity,
            'stock_before'  => $before,
            'stock_after'   => $product->fresh()->stock,
            'note'          => $request->note ?? 'Entrada de proveedor',
        ]);

        return back()->with('success', "Stock actualizado: {$product->fresh()->stock} unidades.");
    }
}