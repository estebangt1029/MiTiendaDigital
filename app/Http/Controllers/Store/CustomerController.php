<?php
namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    private function currentStore()
    {
        return Store::where('owner_id', Auth::guard('owner')->id())
                    ->findOrFail(session('current_store_id'));
    }

    public function index(Request $request)
    {
        $store = $this->currentStore();
        $query = $store->customers()->where('active', true);

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                  ->orWhere('phone', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->filled('filter')) {
            if ($request->filter === 'con_deuda') {
                $query->where('total_debt', '>', 0);
            } elseif ($request->filter === 'sin_deuda') {
                $query->where('total_debt', 0);
            }
        }

        $customers  = $query->orderBy('name')->get();
        $totalDebt  = $store->customers()->where('active', true)->sum('total_debt');
        $withDebt   = $store->customers()->where('active', true)->where('total_debt', '>', 0)->count();

        return view('store.customers.index', compact('store', 'customers', 'totalDebt', 'withDebt'));
    }

    public function create()
    {
        $store = $this->currentStore();
        return view('store.customers.create', compact('store'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $this->currentStore()->customers()->create($request->only('name', 'phone', 'address'));
        return redirect()->route('store.customers.index')->with('success', 'Cliente creado.');
    }

    public function show(Customer $customer)
    {
        $store = $this->currentStore();
        abort_if($customer->store_id !== $store->id, 403);

        $sales    = $customer->sales()
                             ->with('items.product')
                             ->latest()
                             ->get();
        $payments = $customer->payments()->latest()->get();

        return view('store.customers.show', compact('store', 'customer', 'sales', 'payments'));
    }

    public function edit(Customer $customer)
    {
        $store = $this->currentStore();
        abort_if($customer->store_id !== $store->id, 403);
        return view('store.customers.edit', compact('store', 'customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        abort_if($customer->store_id !== $this->currentStore()->id, 403);
        $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);
        $customer->update($request->only('name', 'phone', 'address'));
        return redirect()->route('store.customers.show', $customer)->with('success', 'Cliente actualizado.');
    }

    public function destroy(Customer $customer)
    {
        abort_if($customer->store_id !== $this->currentStore()->id, 403);
        $customer->update(['active' => false]);
        return redirect()->route('store.customers.index')->with('success', 'Cliente desactivado.');
    }
}