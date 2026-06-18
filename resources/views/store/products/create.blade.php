<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Producto</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="max-w-lg mx-auto mt-10 px-4">
        <div class="bg-white rounded-xl shadow p-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold">Nuevo producto</h2>
                <a href="{{ route('store.products.index') }}" class="text-gray-400 hover:text-gray-600 text-sm">← Volver</a>
            </div>

            @if($errors->any())
                <div class="bg-red-100 text-red-700 p-3 rounded mb-4">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('store.products.store') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Nombre *</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Código de barras</label>
                    <div class="flex gap-2">
                        <input type="text" name="barcode" id="barcode" value="{{ old('barcode') }}"
                            class="flex-1 border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            placeholder="Escanea o escribe el código">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Categoría</label>
                    <select name="category_id" class="w-full border rounded-lg px-3 py-2 focus:outline-none">
                        <option value="">Sin categoría</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Precio venta *</label>
                        <input type="number" name="price" value="{{ old('price') }}" step="0.01" min="0"
                            class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Costo compra</label>
                        <input type="number" name="cost" value="{{ old('cost', 0) }}" step="0.01" min="0"
                            class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium mb-1">Stock inicial *</label>
                        <input type="number" name="stock" value="{{ old('stock', 0) }}" min="0"
                            class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Stock mínimo *</label>
                        <input type="number" name="min_stock" value="{{ old('min_stock', 5) }}" min="0"
                            class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                    </div>
                </div>
                <div class="flex gap-3">
                    <button type="submit"
                        class="flex-1 bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 font-medium">
                        Guardar producto
                    </button>
                    <a href="{{ route('store.products.index') }}"
                        class="flex-1 text-center border py-2 rounded-lg hover:bg-gray-50 text-gray-600">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>