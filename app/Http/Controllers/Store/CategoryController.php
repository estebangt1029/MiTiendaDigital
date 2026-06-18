<?php
namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    private function currentStore()
    {
        return Store::where('owner_id', Auth::guard('owner')->id())
                    ->findOrFail(session('current_store_id'));
    }

    public function index()
    {
        $store      = $this->currentStore();
        $categories = $store->categories()->withCount('products')->get();
        return view('store.categories.index', compact('store', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'color' => 'nullable|string|max:7',
        ]);

        $this->currentStore()->categories()->create($request->only('name', 'color'));
        return back()->with('success', 'Categoría creada.');
    }

    public function update(Request $request, Category $category)
    {
        abort_if($category->store_id !== $this->currentStore()->id, 403);
        $request->validate([
            'name'  => 'required|string|max:255',
            'color' => 'nullable|string|max:7',
        ]);
        $category->update($request->only('name', 'color'));
        return back()->with('success', 'Categoría actualizada.');
    }

    public function destroy(Category $category)
    {
        abort_if($category->store_id !== $this->currentStore()->id, 403);
        $category->delete();
        return back()->with('success', 'Categoría eliminada.');
    }
}