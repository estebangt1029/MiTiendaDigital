<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoreUserAuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.storeuser-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::guard('store_user')->attempt($credentials, $request->boolean('remember'))) {
    $request->session()->regenerate();
    $role = Auth::guard('store_user')->user()->role;

    return match($role) {
        'cajero'     => redirect()->route('employee.sales.index'),
        'inventario' => redirect()->route('employee.products.index'),
        'admin'      => redirect()->route('employee.sales.index'),
        default      => redirect()->route('storeuser.dashboard'),
    };
}

        return back()->withErrors(['email' => 'Credenciales incorrectas.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('store_user')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('storeuser.login');
    }
}