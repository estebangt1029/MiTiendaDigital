<nav class="bg-indigo-600 text-white px-6 py-4">
    <div class="max-w-6xl mx-auto flex justify-between items-center">
        <div class="flex items-center gap-6">
            <a href="{{ route('owner.stores.index') }}" class="hover:underline text-sm opacity-75">← Mis tiendas</a>
            <span class="font-bold text-lg">{{ session('store_name', 'Tienda') }}</span>
            <div class="flex gap-4 text-sm">
                <a href="{{ route('store.products.index') }}"
                   class="hover:underline {{ request()->routeIs('store.products.*') ? 'underline font-semibold' : 'opacity-80' }}">
                    📦 Inventario
                </a>
                <a href="{{ route('store.customers.index') }}"
                   class="hover:underline {{ request()->routeIs('store.customers.*') ? 'underline font-semibold' : 'opacity-80' }}">
                    👥 Clientes
                </a>
                <a href="{{ route('store.sales.index') }}"
   class="hover:underline {{ request()->routeIs('store.sales.*') ? 'underline font-semibold' : 'opacity-80' }}">
    🛒 Ventas
</a>
<a href="{{ route('store.reports.index') }}"
   class="hover:underline {{ request()->routeIs('store.reports.*') ? 'underline font-semibold' : 'opacity-80' }}">
    📊 Reportes
</a>
            </div>
        </div>
        <form method="POST" action="{{ route('owner.logout') }}">
            @csrf
            <button class="bg-indigo-800 px-3 py-1 rounded text-sm hover:bg-indigo-900">Salir</button>
        </form>
    </div>
</nav>