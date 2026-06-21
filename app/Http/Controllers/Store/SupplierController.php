<?php
namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    private function currentStore()
    {
        return Store::where('owner_id', Auth::guard('owner')->id())
                    ->findOrFail(session('current_store_id'));
    }

    public function index(Request $request)
    {
        $store = $this->currentStore();
        $query = $store->suppliers()->where('active', true);

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                  ->orWhere('phone', 'like', '%'.$request->search.'%')
                  ->orWhere('contact_name', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->filled('filter')) {
            if ($request->filter === 'con_deuda') {
                $query->where('total_debt', '>', 0);
            } elseif ($request->filter === 'sin_deuda') {
                $query->where('total_debt', 0);
            }
        }

        $suppliers = $query->orderBy('name')->get();
        $totalDebt = $store->suppliers()->where('active', true)->sum('total_debt');
        $withDebt  = $store->suppliers()->where('active', true)->where('total_debt', '>', 0)->count();

        return view('store.suppliers.index', compact('store', 'suppliers', 'totalDebt', 'withDebt'));
    }

    public function create()
    {
        $store = $this->currentStore();
        return view('store.suppliers.create', compact('store'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'phone'        => 'nullable|string|max:20',
            'email'        => 'nullable|email|max:255',
            'address'      => 'nullable|string|max:255',
            'notes'        => 'nullable|string|max:500',
        ]);

        $this->currentStore()->suppliers()->create(
            $request->only('name', 'contact_name', 'phone', 'email', 'address', 'notes')
        );

        return redirect()->route('store.suppliers.index')->with('success', 'Proveedor creado.');
    }

    public function show(Supplier $supplier)
    {
        $store = $this->currentStore();
        abort_if($supplier->store_id !== $store->id, 403);

        $purchases = $supplier->purchases()
                              ->with('items.product')
                              ->latest()
                              ->get();
        $payments  = $supplier->payments()->latest()->get();

        return view('store.suppliers.show', compact('store', 'supplier', 'purchases', 'payments'));
    }

    public function edit(Supplier $supplier)
    {
        $store = $this->currentStore();
        abort_if($supplier->store_id !== $store->id, 403);
        return view('store.suppliers.edit', compact('store', 'supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        abort_if($supplier->store_id !== $this->currentStore()->id, 403);

        $request->validate([
            'name'         => 'required|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'phone'        => 'nullable|string|max:20',
            'email'        => 'nullable|email|max:255',
            'address'      => 'nullable|string|max:255',
            'notes'        => 'nullable|string|max:500',
        ]);

        $supplier->update($request->only('name', 'contact_name', 'phone', 'email', 'address', 'notes'));
        return redirect()->route('store.suppliers.show', $supplier)->with('success', 'Proveedor actualizado.');
    }

    public function destroy(Supplier $supplier)
    {
        abort_if($supplier->store_id !== $this->currentStore()->id, 403);
        $supplier->update(['active' => false]);
        return redirect()->route('store.suppliers.index')->with('success', 'Proveedor desactivado.');
    }
}