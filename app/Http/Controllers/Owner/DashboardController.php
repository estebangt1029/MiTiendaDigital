<?php
namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $owner  = Auth::guard('owner')->user();
        $stores = $owner->stores()->withCount(['products', 'customers'])->get();

        // Separar tiendas activas de pendientes
        $activeStores  = $stores->where('active', true);
        $pendingStores = $stores->where('active', false);

        // Obtener suscripciones pendientes
        $pendingSubs = Subscription::whereIn('store_id', $pendingStores->pluck('id'))
                                   ->where('status', 'pending')
                                   ->with('store')
                                   ->get();

        return view('owner.dashboard', compact('owner', 'activeStores', 'pendingStores', 'pendingSubs'));
    }
}