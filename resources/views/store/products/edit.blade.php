@extends('layouts.store')
@section('title', 'Editar producto')
@section('page-title', 'Editar producto')
@section('page-subtitle', $product->name)

@section('content')
    <div class="max-w-lg">
        <div class="bg-white rounded-xl shadow p-8">
            <form method="POST" action="{{ route('store.products.update', $product) }}">
                @csrf @method('PUT')
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Nombre *</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Código de barras</label>
                    <input type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Categoría</label>
                    <select name="category_id" class="w-full border rounded-lg px-3 py-2 focus:outline-none">
                        <option value="">Sin categoría</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}"
                                {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Precio venta *</label>
                        <input type="number" name="price" value="{{ old('price', $product->price) }}"
                               step="0.01" min="0" required
                            class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Costo compra</label>
                        <input type="number" name="cost" value="{{ old('cost', $product->cost) }}"
                               step="0.01" min="0"
                            class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium mb-1">Stock mínimo *</label>
                    <input type="number" name="min_stock" value="{{ old('min_stock', $product->min_stock) }}"
                           min="0" required
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <p class="text-xs text-gray-400 mt-1">Stock actual: <strong>{{ $product->stock }}</strong> unidades</p>
                </div>
                <div class="flex gap-3">
                    <button type="submit"
                        class="flex-1 bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 font-medium">
                        Guardar cambios
                    </button>
                    <a href="{{ route('store.products.index') }}"
                        class="flex-1 text-center border py-2 rounded-lg hover:bg-gray-50 text-gray-600">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection