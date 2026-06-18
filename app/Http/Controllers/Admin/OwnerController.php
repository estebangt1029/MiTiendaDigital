<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class OwnerController extends Controller
{
    public function index(Request $request)
    {
        $query = Owner::withCount('stores');
        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%')
                  ->orWhere('email', 'like', '%'.$request->search.'%');
        }
        $owners = $query->latest()->get();
        return view('admin.owners.index', compact('owners'));
    }

    public function show(Owner $owner)
    {
        $owner->load(['stores.subscriptions']);
        return view('admin.owners.show', compact('owner'));
    }

    public function create()
    {
        return view('admin.owners.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:owners,email',
            'password' => 'required|min:6',
            'phone'    => 'nullable|string|max:20',
        ]);

        Owner::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'phone'    => $request->phone,
        ]);

        return redirect()->route('admin.owners.index')->with('success', 'Dueño creado exitosamente.');
    }

    public function toggleActive(Owner $owner)
    {
        $owner->update(['active' => !$owner->active]);
        $status = $owner->active ? 'activado' : 'desactivado';
        return back()->with('success', "Dueño {$status} exitosamente.");
    }
}