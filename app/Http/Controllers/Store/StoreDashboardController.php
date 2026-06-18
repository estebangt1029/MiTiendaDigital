<?php
namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class StoreDashboardController extends Controller
{
    public function index()
    {
        $user  = Auth::guard('store_user')->user();
        $store = $user->store;
        return view('store.dashboard', compact('user', 'store'));
    }
}