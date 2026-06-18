<?php
namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\StoreUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StoreUserController extends Controller
{
    private function currentStore()
    {
        return Store::where('owner_id', Auth::guard('owner')->id())
                    ->findOrFail(session('current_store_id'));
    }

    public function index()
    {
        $store = $this->currentStore();
        $users = $store->users()->orderBy('name')->get();
        return view('store.users.index', compact('store', 'users'));
    }

    public function create()
    {
        $store = $this->currentStore();
        return view('store.users.create', compact('store'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:store_users,email',
            'password' => 'required|string|min:6|confirmed',
            'role'     => 'required|in:admin,cajero,inventario',
        ]);

        $this->currentStore()->users()->create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        return redirect()->route('store.users.index')->with('success', 'Empleado creado exitosamente.');
    }

    public function edit(StoreUser $storeUser)
    {
        $store = $this->currentStore();
        abort_if($storeUser->store_id !== $store->id, 403);
        return view('store.users.edit', compact('store', 'storeUser'));
    }

    public function update(Request $request, StoreUser $storeUser)
    {
        abort_if($storeUser->store_id !== $this->currentStore()->id, 403);

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:store_users,email,'.$storeUser->id,
            'role'     => 'required|in:admin,cajero,inventario',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $data = $request->only('name', 'email', 'role');
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $storeUser->update($data);
        return redirect()->route('store.users.index')->with('success', 'Empleado actualizado.');
    }

    public function destroy(StoreUser $storeUser)
    {
        abort_if($storeUser->store_id !== $this->currentStore()->id, 403);
        $storeUser->update(['active' => false]);
        return redirect()->route('store.users.index')->with('success', 'Empleado desactivado.');
    }
}