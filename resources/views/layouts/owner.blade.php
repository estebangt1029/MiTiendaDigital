<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel') — Gestión de Tiendas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @stack('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-50 min-h-screen">

    @hasSection('no-nav')
    @else
    <nav class="bg-white border-b shadow-sm px-6 py-4 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                </svg>
            </div>
            <span class="font-bold text-gray-800">Gestión de Tiendas</span>
        </div>
        <div class="flex items-center gap-4">
            <span class="text-sm text-gray-500">{{ Auth::guard('owner')->user()->name ?? '' }}</span>
            <form method="POST" action="{{ route('owner.logout') }}">
                @csrf
                <button class="text-sm text-red-500 hover:text-red-700">Salir</button>
            </form>
        </div>
    </nav>
    @endif

    @if(session('success'))
        <div class="max-w-5xl mx-auto mt-4 px-4">
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @yield('content')

    @stack('scripts')
</body>
</html>