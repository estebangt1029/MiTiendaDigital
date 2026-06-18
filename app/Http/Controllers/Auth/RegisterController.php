<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Models\Store;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function show(Request $request)
{
    $plans      = Subscription::plans();
    $selectedPlan = $request->get('plan', '1_month');
    return view('auth.register', compact('plans', 'selectedPlan'));
}

    public function register(Request $request)
    {
        $plans = Subscription::plans();

        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:owners,email',
            'phone'         => 'nullable|string|max:20',
            'password'      => 'required|min:6|confirmed',
            'store_name'    => 'required|string|max:255',
            'store_address' => 'nullable|string|max:255',
            'plan'          => 'required|in:'.implode(',', array_keys($plans)),
        ]);

        $selectedPlan = $plans[$request->plan];

        DB::transaction(function () use ($request, $selectedPlan) {
            $owner = Owner::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'phone'    => $request->phone,
                'password' => Hash::make($request->password),
                'active'   => false, // inactivo hasta que confirmes
            ]);

            $store = Store::create([
                'owner_id' => $owner->id,
                'name'     => $request->store_name,
                'address'  => $request->store_address,
                'active'   => false, // inactiva hasta confirmar
            ]);

            Subscription::create([
                'owner_id'   => $owner->id,
                'store_id'   => $store->id,
                'plan'       => $request->plan,
                'price'      => $selectedPlan['price'],
                'start_date' => now(),
                'end_date'   => now()->addMonths($selectedPlan['months']),
                'active'     => false,
                'status'     => 'pending',
            ]);
        });

        return redirect()->route('owner.login')
                         ->with('success', '¡Registro exitoso! Tu cuenta está pendiente de activación. Te notificaremos cuando esté lista.');
    }
}