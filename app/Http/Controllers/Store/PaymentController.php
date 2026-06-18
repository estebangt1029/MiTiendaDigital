<?php
namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    private function currentStore()
    {
        return Store::where('owner_id', Auth::guard('owner')->id())
                    ->findOrFail(session('current_store_id'));
    }

    public function store(Request $request, Sale $sale)
{
    $store = $this->currentStore();
    abort_if($sale->store_id !== $store->id, 403);

    $request->validate([
        'amount' => 'required|numeric|min:0.01|max:'.$sale->debt,
        'method' => 'required|in:efectivo,transferencia,nequi,daviplata,otro',
        'notes'  => 'nullable|string|max:255',
    ]);

    DB::transaction(function () use ($request, $sale, $store) {
        Payment::create([
            'sale_id'       => $sale->id,
            'customer_id'   => $sale->customer_id, // puede ser null
            'store_id'      => $store->id,
            'store_user_id' => null,
            'amount'        => $request->amount,
            'method'        => $request->method,
            'notes'         => $request->notes,
        ]);

        $sale->increment('paid', $request->amount);
        $sale->decrement('debt', $request->amount);

        $newDebt = $sale->fresh()->debt;
        $sale->update([
            'status' => $newDebt <= 0 ? 'pagada' : 'parcial'
        ]);

        // Solo actualizar deuda del cliente si tiene uno asignado
        if ($sale->customer_id) {
            Customer::find($sale->customer_id)->decrement('total_debt', $request->amount);
        }
    });

    return back()->with('success', 'Abono registrado correctamente.');
}
}