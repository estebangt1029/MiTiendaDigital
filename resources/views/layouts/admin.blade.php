<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — Super Admin · MiTiendaDigital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        * { font-family: 'Inter', sans-serif; }

        #sidebar {
            transition: transform .28s cubic-bezier(.4,0,.2,1);
            background: linear-gradient(180deg, #020617 0%, #0f172a 40%, #020617 100%);
        }
        #sidebar-overlay { transition: opacity .28s ease; }

        .nav-link {
            display:flex; align-items:center; gap:14px;
            padding:12px 14px; border-radius:16px;
            font-size:.92rem; font-weight:500; transition:.25s;
            position:relative;
        }
        .nav-link.inactive { color:#cbd5e1; }
        .nav-link.inactive svg { color:#94a3b8; }
        .nav-link.inactive:hover { background:rgba(255,255,255,.05); color:white; transform:translateX(3px); }
        .nav-link.inactive:hover svg { color:white; }
        .nav-link.active {
            color:white;
            background:rgba(99,102,241,.15);
            border:1px solid rgba(99,102,241,.35);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.05), 0 10px 20px rgba(99,102,241,.15);
        }
        .nav-link.active svg { color:#818cf8; }

        .section-title {
            color:#64748b; font-size:.72rem; font-weight:700;
            letter-spacing:.14em; text-transform:uppercase; padding-left:14px;
        }

        @media (max-width:1023px){
            #sidebar { transform:translateX(-100%); }
            #sidebar.open { transform:translateX(0); }
        }

        ::-webkit-scrollbar { width:5px; }
        ::-webkit-scrollbar-thumb { background:#475569; border-radius:999px; }

        .hamburger-line { transition: transform .25s ease, opacity .2s ease, top .25s ease, bottom .25s ease; }
        #hamburger-btn.open .hamburger-line:nth-child(1){ top:50%; transform:translateY(-50%) rotate(45deg); }
        #hamburger-btn.open .hamburger-line:nth-child(2){ opacity:0; }
        #hamburger-btn.open .hamburger-line:nth-child(3){ bottom:50%; transform:translateY(50%) rotate(-45deg); }

        [x-cloak] { display:none; }
    </style>
    @stack('styles')
    <meta name="theme-color" content="#000000">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-slate-50 min-h-screen">

{{-- ══ OVERLAY (móvil) ══ --}}
<div id="sidebar-overlay"
     class="fixed inset-0 bg-black/50 z-40 opacity-0 pointer-events-none lg:hidden"
     onclick="closeSidebar()"></div>

{{-- ══ SIDEBAR ══ --}}
<aside id="sidebar"
       class="fixed top-0 left-0 h-full w-72 z-50 flex flex-col border-r border-white/5 shadow-2xl lg:translate-x-0">

    {{-- Logo --}}
    <div class="p-5">
        <div class="rounded-3xl border border-white/10 bg-white/5 p-4 backdrop-blur">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-slate-600 to-slate-800 flex items-center justify-center shadow-lg shadow-black/30">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] uppercase tracking-[.2em] text-slate-400">Panel</p>
                    <h2 class="text-white font-bold truncate">Super Admin</h2>
                    <p class="text-xs text-slate-500 truncate">{{ Auth::guard('admin')->user()->name ?? '' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Nav principal --}}
    <nav class="flex-1 px-3 py-2 space-y-2 overflow-y-auto">
        <p class="section-title mb-2">General</p>

        <a href="{{ route('admin.dashboard') }}"
           class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : 'inactive' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('admin.subscriptions.pending') }}"
           class="nav-link {{ request()->routeIs('admin.subscriptions.pending') ? 'active' : 'inactive' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Pendientes de activar</span>
        </a>

        <div class="pt-3 mt-2">
            <p class="section-title mb-2">Gestión</p>

            <a href="{{ route('admin.stores.index') }}"
               class="nav-link {{ request()->routeIs('admin.stores.*') ? 'active' : 'inactive' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                </svg>
                <span>Tiendas</span>
            </a>

            <a href="{{ route('admin.owners.index') }}"
               class="nav-link {{ request()->routeIs('admin.owners.*') ? 'active' : 'inactive' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                </svg>
                <span>Dueños</span>
            </a>

            <a href="{{ route('admin.subscriptions.index') }}"
               class="nav-link {{ request()->routeIs('admin.subscriptions.index') ? 'active' : 'inactive' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                <span>Suscripciones</span>
            </a>
        </div>
    </nav>

    {{-- Footer sidebar --}}
    <div class="p-4 space-y-2">
        <form method="POST" action="{{ route('admin.logout') }}">
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
<div class="lg:ml-72 flex flex-col min-h-screen">

    {{-- Topbar --}}
    <header class="bg-white border-b border-slate-200 px-4 lg:px-6 py-3 flex justify-between items-center sticky top-0 z-30 shadow-sm">
        <div class="flex items-center gap-3">
            <button id="hamburger-btn"
                    onclick="toggleSidebar()"
                    class="lg:hidden p-2 rounded-xl text-slate-500 hover:bg-slate-100 transition-colors relative w-9 h-9 flex items-center justify-center">
                <span class="sr-only">Abrir menú</span>
                <span class="hamburger-icon block relative w-5 h-4">
                    <span class="hamburger-line absolute left-0 top-0 w-5 h-0.5 bg-current rounded-full"></span>
                    <span class="hamburger-line absolute left-0 top-1/2 -translate-y-1/2 w-5 h-0.5 bg-current rounded-full"></span>
                    <span class="hamburger-line absolute left-0 bottom-0 w-5 h-0.5 bg-current rounded-full"></span>
                </span>
            </button>
            <div>
                <h2 class="font-semibold text-slate-800 text-sm lg:text-base leading-tight">@yield('page-title', 'Dashboard')</h2>
                <p class="text-xs text-slate-400 hidden sm:block">@yield('page-subtitle', '')</p>
            </div>
        </div>

        <div class="flex items-center gap-2 lg:gap-3">
            @yield('header-actions')
            <div class="w-8 h-8 rounded-full bg-slate-800 text-white flex items-center justify-center text-sm font-bold flex-shrink-0">
                {{ substr(Auth::guard('admin')->user()->name ?? 'A', 0, 1) }}
            </div>
        </div>
    </header>

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
        Super Admin Panel · MiTiendaDigital &copy; {{ date('Y') }}
    </footer>
</div>

<script>
function openSidebar() {
    document.getElementById('sidebar').classList.add('open');
    document.getElementById('hamburger-btn').classList.add('open');
    const overlay = document.getElementById('sidebar-overlay');
    overlay.classList.remove('pointer-events-none');
    overlay.style.opacity = '1';
    document.body.style.overflow = 'hidden';
}
function closeSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('hamburger-btn').classList.remove('open');
    const overlay = document.getElementById('sidebar-overlay');
    overlay.style.opacity = '0';
    setTimeout(() => overlay.classList.add('pointer-events-none'), 280);
    document.body.style.overflow = '';
}
function toggleSidebar() {
    document.getElementById('sidebar').classList.contains('open') ? closeSidebar() : openSidebar();
}

let touchStartX = 0;
document.addEventListener('touchstart', e => { touchStartX = e.touches[0].clientX; });
document.addEventListener('touchend', e => {
    if (touchStartX > 20 && e.changedTouches[0].clientX < touchStartX - 60) closeSidebar();
});

document.querySelectorAll('#sidebar a, #sidebar button[type="submit"]').forEach(el => {
    el.addEventListener('click', () => {
        if (window.innerWidth < 1024) closeSidebar();
    });
});
</script>

@stack('scripts')
</body>
</html>