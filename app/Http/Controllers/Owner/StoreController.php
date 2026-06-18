<?php
namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{
    private function owner()
    {
        return Auth::guard('owner')->user();
    }

    public function index()
    {
        $stores = $this->owner()->stores()->withCount(['products', 'customers'])->get();
        return view('owner.stores.index', compact('stores'));
    }

    public function create()
    {
        $plans = Subscription::plans();
        return view('owner.stores.create', compact('plans'));
    }

    public function store(Request $request)
    {
        $plans = Subscription::plans();

        $request->validate([
            'name'    => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone'   => 'nullable|string|max:20',
            'plan'    => 'required|in:'.implode(',', array_keys($plans)),
        ]);

        $selectedPlan = $plans[$request->plan];

        DB::transaction(function () use ($request, $selectedPlan) {
            $store = $this->owner()->stores()->create([
                'name'    => $request->name,
                'address' => $request->address,
                'phone'   => $request->phone,
                'active'  => false, // inactiva hasta confirmar pago
            ]);

            Subscription::create([
                'owner_id'   => $this->owner()->id,
                'store_id'   => $store->id,
                'plan'       => $request->plan,
                'price'      => $selectedPlan['price'],
                'start_date' => now(),
                'end_date'   => now()->addMonths($selectedPlan['months']),
                'active'     => false,
                'status'     => 'pending',
            ]);
        });

        return redirect()->route('owner.dashboard')
                         ->with('success', 'Tienda creada. Pendiente de activación tras confirmar el pago.');
    }

    public function edit(Store $store)
    {
        abort_if($store->owner_id !== $this->owner()->id, 403);
        return view('owner.stores.edit', compact('store'));
    }

    public function update(Request $request, Store $store)
    {
        abort_if($store->owner_id !== $this->owner()->id, 403);

        $request->validate([
            'name'    => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone'   => 'nullable|string|max:20',
        ]);

        $store->update($request->only('name', 'address', 'phone'));
        return redirect()->route('owner.stores.index')->with('success', 'Tienda actualizada.');
    }

    public function destroy(Store $store)
    {
        abort_if($store->owner_id !== $this->owner()->id, 403);
        $store->update(['active' => false]);
        return redirect()->route('owner.stores.index')->with('success', 'Tienda desactivada.');
    }
}