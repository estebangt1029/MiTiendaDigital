<?php
namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SupplierPaymentController extends Controller
{
    private function currentStore()
    {
        return Store::where('owner_id', Auth::guard('owner')->id())
                    ->findOrFail(session('current_store_id'));
    }

    public function store(Request $request, Supplier $supplier)
    {
        $store = $this->currentStore();
        abort_if($supplier->store_id !== $store->id, 403);

        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:'.$supplier->total_debt,
            'method' => 'nullable|string|max:50',
            'notes'  => 'nullable|string|max:255',
        ], [
            'amount.max' => 'El abono no puede ser mayor a la deuda actual ($'.number_format($supplier->total_debt, 0, ',', '.').').',
        ]);

        DB::transaction(function () use ($request, $store, $supplier) {
            $supplier->payments()->create([
                'store_id'      => $store->id,
                'store_user_id' => null,
                'purchase_id'   => null, // abono general, no atado a una compra específica
                'amount'        => $request->amount,
                'method'        => $request->method ?? 'efectivo',
                'notes'         => $request->notes,
            ]);

            $supplier->decrement('total_debt', $request->amount);
        });

        return back()->with('success', 'Abono registrado correctamente.');
    }
}