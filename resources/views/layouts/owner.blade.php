<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Tienda') — {{ session('store_name', 'Mi Tienda') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        * { font-family: 'Inter', sans-serif; }

        /* Sidebar transition */
        #sidebar {
            transition: transform 0.28s cubic-bezier(.4,0,.2,1);
        }
        #sidebar-overlay {
            transition: opacity 0.28s ease;
        }

        /* Nav link activo */
        .nav-link { @apply flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150; }
        .nav-link:hover { @apply bg-white/10; }
        .nav-link.active { @apply bg-white text-indigo-700 shadow-sm; }
        .nav-link.inactive { @apply text-indigo-100; }

        /* Oculta el sidebar en móvil por defecto */
        @media (max-width: 1023px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.open { transform: translateX(0); }
        }

        /* Scrollbar delgado */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #c7d2fe; border-radius: 10px; }

        [x-cloak] { display: none; }
    </style>
    @stack('styles')
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#4338ca">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="MiTienda">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-slate-50 min-h-screen">

{{-- ══ OVERLAY (móvil) ══ --}}
<div id="sidebar-overlay"
     class="fixed inset-0 bg-black/50 z-40 opacity-0 pointer-events-none lg:hidden"
     onclick="closeSidebar()"></div>

{{-- ══ SIDEBAR ══ --}}
<aside id="sidebar"
       class="fixed top-0 left-0 h-full w-64 bg-gradient-to-b from-indigo-700 to-indigo-800 z-50 flex flex-col shadow-2xl lg:translate-x-0">

    {{-- Logo --}}
    <div class="px-5 py-5 border-b border-indigo-600/60">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-indigo-300 text-xs uppercase tracking-widest leading-none mb-0.5">Tienda activa</p>
                <h1 class="text-white font-bold text-sm leading-tight truncate">{{ session('store_name', 'Mi Tienda') }}</h1>
            </div>
        </div>
    </div>

    {{-- Nav principal --}}
    <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
        <p class="text-indigo-400 text-xs uppercase tracking-widest px-3 pb-2 pt-1">Principal</p>

        <a href="{{ route('store.products.index') }}"
           class="nav-link {{ request()->routeIs('store.products.*') ? 'active' : 'inactive' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
            </svg>
            <span>Inventario</span>
        </a>

        <a href="{{ route('store.sales.create') }}"
           class="nav-link {{ request()->routeIs('store.sales.create') ? 'active' : 'inactive' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4v16m8-8H4"/>
            </svg>
            <span>Nueva venta</span>
        </a>

        <a href="{{ route('store.sales.index') }}"
           class="nav-link {{ request()->routeIs('store.sales.index') || request()->routeIs('store.sales.show') ? 'active' : 'inactive' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <span>Historial ventas</span>
        </a>

        <a href="{{ route('store.customers.index') }}"
           class="nav-link {{ request()->routeIs('store.customers.*') ? 'active' : 'inactive' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
            </svg>
            <span>Clientes / Fiados</span>
        </a>

        <a href="{{ route('store.reports.index') }}"
           class="nav-link {{ request()->routeIs('store.reports.*') ? 'active' : 'inactive' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <span>Reportes</span>
        </a>

        <div class="pt-3 mt-3 border-t border-indigo-600/50">
            <p class="text-indigo-400 text-xs uppercase tracking-widest px-3 pb-2">Configuración</p>

            <a href="{{ route('store.users.index') }}"
               class="nav-link {{ request()->routeIs('store.users.*') ? 'active' : 'inactive' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span>Empleados</span>
            </a>

            <a href="{{ route('store.categories.index') }}"
               class="nav-link {{ request()->routeIs('store.categories.*') ? 'active' : 'inactive' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                <span>Categorías</span>
            </a>
        </div>
    </nav>

    {{-- Footer sidebar --}}
    <div class="px-3 py-3 border-t border-indigo-600/50 space-y-0.5">
        <a href="{{ route('owner.stores.index') }}" class="nav-link inactive">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span>Mis tiendas</span>
        </a>
        <form method="POST" action="{{ route('owner.logout') }}">
            @csrf
            <button type="submit" class="nav-link inactive w-full text-left">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span>Cerrar sesión</span>
            </button>
        </form>
    </div>
</aside>

{{-- ══ CONTENIDO PRINCIPAL ══ --}}
<div class="lg:ml-64 flex flex-col min-h-screen">

    {{-- Topbar --}}
    <header class="bg-white border-b border-slate-200 px-4 lg:px-6 py-3 flex justify-between items-center sticky top-0 z-30 shadow-sm">
        {{-- Botón hamburguesa (solo móvil) --}}
        <div class="flex items-center gap-3">
            <button id="hamburger-btn"
                    onclick="openSidebar()"
                    class="lg:hidden p-2 rounded-xl text-slate-500 hover:bg-slate-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <div>
                <h2 class="font-semibold text-slate-800 text-sm lg:text-base leading-tight">@yield('page-title', 'Dashboard')</h2>
                <p class="text-xs text-slate-400 hidden sm:block">@yield('page-subtitle', '')</p>
            </div>
        </div>

        <div class="flex items-center gap-2 lg:gap-3">
            @yield('header-actions')
            <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-sm font-bold flex-shrink-0">
                {{ substr(Auth::guard('owner')->user()->name ?? 'U', 0, 1) }}
            </div>
        </div>
    </header>

    {{-- Alerta suscripción --}}
    @if(isset($subscriptionDaysLeft) && $subscriptionDaysLeft <= 7)
        <div class="mx-4 lg:mx-6 mt-3 bg-amber-50 border border-amber-200 text-amber-700 px-4 py-3 rounded-xl flex items-center justify-between text-sm gap-3">
            <span>⚠ Tu suscripción vence <strong>{{ $subscriptionDaysLeft <= 0 ? 'hoy' : 'en '.$subscriptionDaysLeft.' día(s)' }}</strong></span>
            <span class="text-xs text-amber-500 flex-shrink-0">Contacta al admin</span>
        </div>
    @endif

    {{-- Flash messages --}}
    <div class="px-4 lg:px-6 pt-3 space-y-2">
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-2 text-sm">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-2 text-sm">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"/>
                </svg>
                {{ $errors->first() }}
            </div>
        @endif
    </div>

    {{-- Página --}}
    <main class="flex-1 px-4 lg:px-6 py-4">
        @yield('content')
    </main>

    <footer class="px-6 py-3 text-xs text-slate-400 border-t text-center">
        MiTiendaDigital &copy; {{ date('Y') }} · Hecho en Colombia 🇨🇴
    </footer>
</div>

{{-- Indicador de conexión --}}
<div id="online-indicator"
     class="fixed bottom-4 right-4 text-white text-xs px-3 py-1.5 rounded-full shadow-lg transition-all opacity-0 pointer-events-none z-50">
</div>

<script>
// ── Sidebar hamburguesa ──────────────────────────────────
function openSidebar() {
    document.getElementById('sidebar').classList.add('open');
    const overlay = document.getElementById('sidebar-overlay');
    overlay.classList.remove('pointer-events-none');
    overlay.style.opacity = '1';
    document.body.style.overflow = 'hidden';
}
function closeSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    const overlay = document.getElementById('sidebar-overlay');
    overlay.style.opacity = '0';
    setTimeout(() => overlay.classList.add('pointer-events-none'), 280);
    document.body.style.overflow = '';
}
// Cerrar con swipe hacia la izquierda
let touchStartX = 0;
document.addEventListener('touchstart', e => { touchStartX = e.touches[0].clientX; });
document.addEventListener('touchend', e => {
    if (touchStartX > 20 && e.changedTouches[0].clientX < touchStartX - 60) closeSidebar();
});

// ── Service Worker ──────────────────────────────────────
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(() => {});
    });
}

// ── Estado de conexión ──────────────────────────────────
function updateOnlineStatus() {
    const el = document.getElementById('online-indicator');
    if (navigator.onLine) {
        el.className = 'fixed bottom-4 right-4 bg-emerald-500 text-white text-xs px-3 py-1.5 rounded-full shadow-lg z-50';
        el.textContent = '✓ Conectado';
        el.style.opacity = '1';
        setTimeout(() => { el.style.opacity = '0'; }, 2000);
    } else {
        el.className = 'fixed bottom-4 right-4 bg-red-500 text-white text-xs px-3 py-1.5 rounded-full shadow-lg z-50';
        el.textContent = '⚠ Sin internet';
        el.style.opacity = '1';
    }
}
window.addEventListener('online', updateOnlineStatus);
window.addEventListener('offline', updateOnlineStatus);
</script>

@stack('scripts')
</body>
</html>
