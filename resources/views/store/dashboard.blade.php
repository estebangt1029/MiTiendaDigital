<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Tienda</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-green-600 text-white px-6 py-4 flex justify-between items-center">
        <h1 class="font-bold text-lg">{{ $store->name }}</h1>
        <div class="flex items-center gap-4">
            <span>{{ $user->name }}</span>
            <form method="POST" action="{{ route('storeuser.logout') }}">
                @csrf
                <button class="bg-green-800 px-3 py-1 rounded hover:bg-green-900 text-sm">Salir</button>
            </form>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto mt-10 px-4">
        <h2 class="text-xl font-semibold mb-2">Bienvenido, {{ $user->name }}</h2>
        <p class="text-gray-500">Tienda: {{ $store->name }}</p>
    </div>
</body>
</html>