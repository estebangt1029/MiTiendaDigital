<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Empleados</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-xl shadow w-full max-w-md">
        <div class="flex items-center justify-center gap-2 mb-6">
            <div class="w-8 h-8 rounded-lg bg-green-600 flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <h1 class="text-xl font-bold">Acceso Empleados</h1>
        </div>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 p-3 rounded-lg mb-4 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('storeuser.login') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Correo</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium mb-1">Contraseña</label>
                <input type="password" name="password" required
                    class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <button type="submit"
                class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 font-medium">
                Ingresar
            </button>
        </form>

        <p class="text-center text-sm mt-4 text-gray-500">
            ¿Eres dueño? <a href="{{ route('owner.login') }}" class="text-green-600 hover:underline">Entra aquí</a>
        </p>
    </div>
</body>
</html>