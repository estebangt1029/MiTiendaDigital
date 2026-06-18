<?php
namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    private function store()
    {
        return Auth::guard('store_user')->user()->store;
    }

    public function index(Request $request)
    {
        $store     = $this->store();
        $query     = $store->customers()->where('active', true);
        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }
        $customers = $query->orderBy('name')->get();
        $totalDebt = $store->customers()->where('active', true)->sum('total_debt');
        $withDebt  = $store->customers()->where('active', true)->where('total_debt', '>', 0)->count();
        return view('employee.customers.index', compact('store', 'customers', 'totalDebt', 'withDebt'));
    }

    public function show(Customer $customer)
    {
        abort_if($customer->store_id !== $this->store()->id, 403);
        $sales    = $customer->sales()->with('items.product')->latest()->get();
        $payments = $customer->payments()->latest()->get();
        return view('employee.customers.show', compact('customer', 'sales', 'payments'));
    }

    public function pay(Request $request, Sale $sale = null, Customer $customer)
    {
        abort_if($customer->store_id !== $this->store()->id, 403);
        $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'amount'  => 'required|numeric|min:0.01',
            'method'  => 'required|in:efectivo,transferencia,nequi,daviplata,otro',
        ]);

        $sale  = \App\Models\Sale::findOrFail($request->sale_id);
        $store = $this->store();

        DB::transaction(function () use ($request, $sale, $store, $customer) {
            Payment::create([
                'sale_id'       => $sale->id,
                'customer_id'   => $customer->id,
                'store_id'      => $store->id,
                'store_user_id' => Auth::guard('store_user')->id(),
                'amount'        => $request->amount,
                'method'        => $request->method,
            ]);
            $sale->increment('paid', $request->amount);
            $sale->decrement('debt', $request->amount);
            $sale->update(['status' => $sale->fresh()->debt <= 0 ? 'pagada' : 'parcial']);
            $customer->decrement('total_debt', $request->amount);
        });

        return back()->with('success', 'Abono registrado.');
    }
}