<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Tienda</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="max-w-lg mx-auto mt-10 px-4">
        <div class="bg-white rounded-xl shadow p-8">
            <h2 class="text-xl font-bold mb-6">Editar tienda</h2>

            @if($errors->any())
                <div class="bg-red-100 text-red-700 p-3 rounded mb-4">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('owner.stores.update', $store) }}">
                @csrf @method('PUT')
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Nombre *</label>
                    <input type="text" name="name" value="{{ old('name', $store->name) }}"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Dirección</label>
                    <input type="text" name="address" value="{{ old('address', $store->address) }}"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium mb-1">Teléfono</label>
                    <input type="text" name="phone" value="{{ old('phone', $store->phone) }}"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="flex gap-3">
                    <button type="submit"
                        class="flex-1 bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 font-medium">
                        Guardar cambios
                    </button>
                    <a href="{{ route('owner.stores.index') }}"
                        class="flex-1 text-center border py-2 rounded-lg hover:bg-gray-50 text-gray-600">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>