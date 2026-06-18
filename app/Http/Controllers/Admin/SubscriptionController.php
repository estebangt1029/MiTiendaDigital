<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\Store;
use App\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = Subscription::with(['owner', 'store'])
                            ->orderBy('end_date')
                            ->get();
        return view('admin.subscriptions.index', compact('subscriptions'));
    }

    public function create()
    {
        $owners = Owner::where('active', true)->get();
        $stores = Store::where('active', true)->with('owner')->get();
        return view('admin.subscriptions.create', compact('owners', 'stores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'owner_id'   => 'required|exists:owners,id',
            'store_id'   => 'required|exists:stores,id',
            'plan'       => 'required|in:basic,pro',
            'price'      => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
        ]);

        // Desactivar suscripción anterior de esa tienda
        Subscription::where('store_id', $request->store_id)
                    ->where('active', true)
                    ->update(['active' => false]);

        Subscription::create($request->only(
            'owner_id', 'store_id', 'plan', 'price', 'start_date', 'end_date'
        ) + ['active' => true]);

        return redirect()->route('admin.subscriptions.index')
                         ->with('success', 'Suscripción creada exitosamente.');
    }

    public function renew(Subscription $subscription)
    {
        $newEnd = now()->gt($subscription->end_date)
            ? now()->addMonth()
            : \Carbon\Carbon::parse($subscription->end_date)->addMonth();

        $subscription->update([
            'end_date' => $newEnd,
            'active'   => true,
        ]);

        return back()->with('success', 'Suscripción renovada por 1 mes.');
    }

    public function cancel(Subscription $subscription)
    {
        $subscription->update(['active' => false]);
        return back()->with('success', 'Suscripción cancelada.');
    }

    public function confirm(Subscription $subscription)
{
    DB::transaction(function () use ($subscription) {
        $subscription->update([
            'status'       => 'active',
            'active'       => true,
            'confirmed_at' => now(),
            'start_date'   => now(),
            'end_date'     => now()->addMonths(
                Subscription::plans()[$subscription->plan]['months']
            ),
        ]);

        // Activar la tienda y el dueño
        $subscription->store->update(['active' => true]);
        $subscription->owner->update(['active' => true]);
    });

    return back()->with('success', "Suscripción confirmada. Tienda \"{$subscription->store->name}\" activada.");
}

public function pending()
{
    $subscriptions = Subscription::with(['owner', 'store'])
                        ->where('status', 'pending')
                        ->latest()
                        ->get();
    return view('admin.subscriptions.pending', compact('subscriptions'));
}
}