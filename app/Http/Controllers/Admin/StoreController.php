<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        $query = Store::with('owner')->withCount(['products', 'customers', 'sales']);
        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }
        if ($request->filled('status')) {
            $query->where('active', $request->status === 'active');
        }
        $stores = $query->latest()->get();
        return view('admin.stores.index', compact('stores'));
    }

    public function toggleActive(Store $store)
    {
        $store->update(['active' => !$store->active]);
        $status = $store->active ? 'activada' : 'desactivada';
        return back()->with('success', "Tienda {$status} exitosamente.");
    }
}