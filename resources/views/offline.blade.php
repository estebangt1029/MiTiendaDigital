<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sin conexión</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="text-center px-6">
        <div class="w-20 h-20 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M18.364 5.636a9 9 0 010 12.728M15.536 8.464a5 5 0 010 7.072M6.343 17.657a9 9 0 010-12.728M9.172 15.536a5 5 0 010-7.072M12 12h.01"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Sin conexión</h1>
        <p class="text-gray-500 mb-6">No hay internet disponible. Las ventas que registres se guardarán localmente y se sincronizarán cuando vuelva la conexión.</p>
        <button onclick="window.location.reload()"
            class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700">
            Reintentar
        </button>
    </div>
</body>
</html>