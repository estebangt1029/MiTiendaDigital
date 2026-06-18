<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel') — {{ Auth::guard('store_user')->user()->store->name ?? 'Tienda' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('styles')
    <link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#4f46e5">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="MiTienda">
<link rel="apple-touch-icon" href="/icons/icon-192.png">
<meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<script src="/js/offline-sales.js"></script>
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js')
                .then(reg => console.log('SW registrado:', reg.scope))
                .catch(err => console.log('SW error:', err));
        });
    }

    // Detectar estado de conexión
    function updateOnlineStatus() {
        const indicator = document.getElementById('online-indicator');
        if (!indicator) return;
        if (navigator.onLine) {
            indicator.className = 'fixed bottom-4 right-4 bg-green-500 text-white text-xs px-3 py-1.5 rounded-full shadow-lg transition-all';
            indicator.textContent = '✓ Conectado';
            setTimeout(() => indicator.style.opacity = '0', 2000);
        } else {
            indicator.className = 'fixed bottom-4 right-4 bg-red-500 text-white text-xs px-3 py-1.5 rounded-full shadow-lg';
            indicator.textContent = '⚠ Sin internet';
            indicator.style.opacity = '1';
        }
    }

    window.addEventListener('online', updateOnlineStatus);
    window.addEventListener('offline', updateOnlineStatus);
</script>

<div id="online-indicator" class="fixed bottom-4 right-4 bg-green-500 text-white text-xs px-3 py-1.5 rounded-full shadow-lg opacity-0 transition-all"></div>
<body class="bg-gray-50 min-h-screen flex">

    <aside class="w-60 min-h-screen bg-green-700 flex flex-col fixed top-0 left-0 z-30 shadow-xl">
        <div class="px-5 py-6 border-b border-green-600">
            <p class="text-xs text-green-300 uppercase tracking-widest mb-1">Tienda</p>
            <h1 class="text-white font-bold text-lg leading-tight">
                {{ Auth::guard('store_user')->user()->store->name ?? 'Mi Tienda' }}
            </h1>
            <p class="text-green-300 text-xs mt-1">
                {{ Auth::guard('store_user')->user()->name }}
                <span class="ml-1 bg-green-600 px-1.5 py-0.5 rounded text-green-100 capitalize">
                    {{ Auth::guard('store_user')->user()->role }}
                </span>
            </p>
        </div>

        <nav class="flex-1 px-3 py-4 space-y-1">
            @php $role = Auth::guard('store_user')->user()->role; @endphp

            {{-- Cajero y Admin ven ventas --}}
            @if(in_array($role, ['admin', 'cajero']))
                <a href="{{ route('employee.sales.index') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-all
                          {{ request()->routeIs('employee.sales.*') ? 'bg-white text-green-700 shadow-sm' : 'text-green-100 hover:bg-white/10' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Ventas
                </a>
                <a href="{{ route('employee.customers.index') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-all
                          {{ request()->routeIs('employee.customers.*') ? 'bg-white text-green-700 shadow-sm' : 'text-green-100 hover:bg-white/10' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                    </svg>
                    Clientes
                </a>
            @endif

            {{-- Inventario y Admin ven productos --}}
            @if(in_array($role, ['admin', 'inventario']))
                <a href="{{ route('employee.products.index') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-all
                          {{ request()->routeIs('employee.products.*') ? 'bg-white text-green-700 shadow-sm' : 'text-green-100 hover:bg-white/10' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                    </svg>
                    Inventario
                </a>
            @endif

            {{-- Admin ve reportes --}}
            @if($role === 'admin')
                <a href="{{ route('employee.reports.index') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-all
                          {{ request()->routeIs('employee.reports.*') ? 'bg-white text-green-700 shadow-sm' : 'text-green-100 hover:bg-white/10' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Reportes
                </a>
            @endif
        </nav>

        <div class="px-3 py-4 border-t border-green-600">
            <form method="POST" action="{{ route('storeuser.logout') }}">
                @csrf
                <button class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium text-green-100 hover:bg-white/10 w-full">
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
                <h2 class="font-semibold text-gray-800">@yield('page-title', 'Panel')</h2>
                <p class="text-xs text-gray-400">@yield('page-subtitle', '')</p>
            </div>
            <div class="flex items-center gap-3">
                @yield('header-actions')
                <div class="w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-sm font-bold">
                    {{ substr(Auth::guard('store_user')->user()->name ?? 'E', 0, 1) }}
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
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"/>
                    </svg>
                    {{ $errors->first() }}
                </div>
            @endif
        </div>

        <main class="flex-1 px-6 py-4">
            @if(isset($subscriptionDaysLeft) && $subscriptionDaysLeft <= 7)
    <div class="mx-6 mt-2 bg-amber-50 border border-amber-200 text-amber-700 px-4 py-3 rounded-lg flex items-center justify-between text-sm">
        <span>
            ⚠ Tu suscripción vence
            <strong>
                {{ $subscriptionDaysLeft <= 0 ? 'hoy' : 'en '.$subscriptionDaysLeft.' día(s)' }}
            </strong>
        </span>
        <span class="text-xs text-amber-500">Contacta al administrador para renovar</span>
    </div>
@endif
            @yield('content')
        </main>

        <footer class="px-6 py-3 text-xs text-gray-400 border-t text-center">
            Sistema de gestión de tiendas &copy; {{ date('Y') }}
        </footer>
    </div>

    @stack('scripts')
</body>
</html>