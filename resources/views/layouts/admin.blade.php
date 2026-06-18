<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — Super Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen flex">

    <aside class="w-60 min-h-screen bg-gray-900 flex flex-col fixed top-0 left-0 z-30 shadow-xl">
        <div class="px-5 py-6 border-b border-gray-700">
            <p class="text-xs text-gray-400 uppercase tracking-widest mb-1">Panel</p>
            <h1 class="text-white font-bold text-lg">Super Admin</h1>
            <p class="text-gray-400 text-xs mt-1">{{ Auth::guard('admin')->user()->name ?? '' }}</p>
        </div>

        <nav class="flex-1 px-3 py-4 space-y-1">
            @php
                $links = [
                    ['route' => 'admin.dashboard',            'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 'label' => 'Dashboard'],
                    ['route' => 'admin.owners.index',         'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0', 'label' => 'Dueños'],
                    ['route' => 'admin.stores.index',         'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5', 'label' => 'Tiendas'],
                    ['route' => 'admin.subscriptions.index',  'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z', 'label' => 'Suscripciones'],
                    ['route' => 'admin.subscriptions.pending', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'label' => 'Pendientes'],
                ];
            @endphp

            @foreach($links as $link)
                <a href="{{ route($link['route']) }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-all
                          {{ request()->routeIs($link['route'].'*') ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-300 hover:bg-gray-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $link['icon'] }}"/>
                    </svg>
                    {{ $link['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="px-3 py-4 border-t border-gray-700">
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-gray-300 hover:bg-gray-700 w-full">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Cerrar sesión
                </button>
            </form>
        </div>
    </aside>

    <div class="ml-60 flex-1 flex flex-col min-h-screen">
        <header class="bg-white border-b px-6 py-4 flex justify-between items-center sticky top-0 z-20 shadow-sm">
            <div>
                <h2 class="font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h2>
                <p class="text-xs text-gray-400">@yield('page-subtitle', '')</p>
            </div>
            <div class="flex items-center gap-3">
                @yield('header-actions')
                <div class="w-8 h-8 rounded-full bg-gray-800 text-white flex items-center justify-center text-sm font-bold">
                    {{ substr(Auth::guard('admin')->user()->name ?? 'A', 0, 1) }}
                </div>
            </div>
        </header>

        <div class="px-6 pt-4">
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-2">
                    {{ $errors->first() }}
                </div>
            @endif
        </div>

        <main class="flex-1 px-6 py-4">
            @yield('content')
        </main>

        <footer class="px-6 py-3 text-xs text-gray-400 border-t text-center">
            Super Admin Panel &copy; {{ date('Y') }}
        </footer>
    </div>

    @stack('scripts')
</body>
</html>