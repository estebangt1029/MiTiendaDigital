@extends('layouts.store')
@section('title', 'Inventario')
@section('page-title', 'Inventario')
@section('page-subtitle', 'Productos y stock de la tienda')
@section('header-actions')
    <a href="{{ route('store.products.create') }}"
       class="bg-indigo-600 text-white px-3 py-2 lg:px-4 rounded-xl text-sm hover:bg-indigo-700 font-semibold flex items-center gap-1.5 transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        <span class="hidden sm:inline">Nuevo producto</span>
        <span class="sm:hidden">Nuevo</span>
    </a>
@endsection

@section('content')

    {{-- Alerta stock bajo --}}
    @if($lowStock > 0)
        <div class="bg-amber-50 border border-amber-200 text-amber-700 px-4 py-3 rounded-xl mb-4 flex items-center gap-2 text-sm">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"/>
            </svg>
            <span><strong>{{ $lowStock }} producto(s)</strong> con stock bajo o agotado.</span>
        </div>
    @endif

    {{-- Barra de búsqueda --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 mb-4">
        {{-- Búsqueda y filtros --}}
        <div class="flex flex-col sm:flex-row gap-2">
            <div class="flex-1 relative">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" id="searchInput"
                    placeholder="Buscar producto o código..."
                    class="w-full border border-slate-200 rounded-xl pl-9 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-slate-50"
                    oninput="filterProducts()">
            </div>
            <div class="flex gap-2">
                <select id="categoryFilter" onchange="filterProducts()"
                    class="flex-1 sm:flex-none border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-slate-50">
                    <option value="">Categorías</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
                <select id="statusFilter" onchange="filterProducts()"
                    class="flex-1 sm:flex-none border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-slate-50">
                    <option value="">Estado</option>
                    <option value="ok">Stock OK</option>
                    <option value="low">Stock bajo</option>
                    <option value="out">Agotados</option>
                </select>
                <button onclick="toggleBarcodeScanner()"
                    class="border border-indigo-200 text-indigo-600 px-3 py-2.5 rounded-xl text-sm hover:bg-indigo-50 flex items-center gap-1.5 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                    <span class="hidden sm:inline">Escanear</span>
                </button>
            </div>
        </div>

        {{-- Lector de barras --}}
        <div id="barcodeScanner" class="hidden mt-3">
            <div class="flex gap-2 items-center bg-indigo-50 border border-indigo-200 rounded-xl px-3 py-2.5">
                <svg class="w-4 h-4 text-indigo-500 animate-pulse flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 4a1 1 0 011-1h1a1 1 0 010 2H3a1 1 0 01-1-1zm4 0a1 1 0 011-1h1a1 1 0 010 2H7a1 1 0 01-1-1zm5-1a1 1 0 000 2h1a1 1 0 000-2h-1zm4 0a1 1 0 000 2h1a1 1 0 000-2h-1zM2 8a1 1 0 011-1h1a1 1 0 010 2H3a1 1 0 01-1-1zm13-1a1 1 0 000 2h1a1 1 0 000-2h-1zM2 12a1 1 0 011-1h1a1 1 0 010 2H3a1 1 0 01-1-1zm13-1a1 1 0 000 2h1a1 1 0 000-2h-1z"/>
                </svg>
                <input type="text" id="barcodeInput" autofocus
                    placeholder="Escanea el código de barras..."
                    class="flex-1 bg-transparent text-sm focus:outline-none text-indigo-700"
                    onkeydown="handleBarcode(event)">
                <button onclick="toggleBarcodeScanner()" class="text-indigo-400 hover:text-indigo-600 text-xs">✕</button>
            </div>
        </div>

        <div class="mt-2 flex justify-between items-center">
            <p id="resultCount" class="text-xs text-slate-400">{{ $products->count() }} productos</p>
            <button onclick="clearFilters()" class="text-xs text-indigo-500 hover:underline hidden" id="clearBtn">
                Limpiar filtros
            </button>
        </div>
    </div>

    {{-- Modal agregar stock --}}
    <div id="stockModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4">
        <div class="bg-white rounded-t-3xl sm:rounded-2xl shadow-xl p-6 w-full sm:max-w-sm">
            <div class="w-10 h-1 bg-slate-200 rounded-full mx-auto mb-5 sm:hidden"></div>
            <h3 class="font-bold text-lg mb-0.5" id="modalProductName"></h3>
            <p class="text-slate-400 text-sm mb-5" id="modalProductStock"></p>
            <form method="POST" id="stockForm">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2 text-slate-700">Cantidad a agregar</label>
                    <div class="flex items-center gap-3">
                        <button type="button" onclick="changeStockQty(-1)"
                            class="w-12 h-12 rounded-xl border-2 border-slate-200 text-xl font-bold hover:bg-slate-50 flex items-center justify-center transition-colors">−</button>
                        <input type="number" name="quantity" id="stockQty" value="1" min="1"
                            class="flex-1 border-2 border-slate-200 rounded-xl px-3 py-3 text-center text-xl font-bold focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <button type="button" onclick="changeStockQty(1)"
                            class="w-12 h-12 rounded-xl border-2 border-slate-200 text-xl font-bold hover:bg-slate-50 flex items-center justify-center transition-colors">+</button>
                    </div>
                </div>
                <div class="mb-5">
                    <label class="block text-sm font-medium mb-1.5 text-slate-700">Nota (opcional)</label>
                    <input type="text" name="note" placeholder="Ej: Llegó pedido proveedor"
                        class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-slate-50">
                </div>
                <div class="flex gap-3">
                    <button type="submit"
                        class="flex-1 bg-indigo-600 text-white py-3 rounded-xl font-semibold hover:bg-indigo-700 transition-colors shadow-sm">
                        Agregar stock
                    </button>
                    <button type="button" onclick="closeStockModal()"
                        class="flex-1 border border-slate-200 py-3 rounded-xl text-slate-600 hover:bg-slate-50 transition-colors">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ══ VISTA MÓVIL: cards ══ --}}
    <div class="block lg:hidden space-y-3" id="productCardsMobile">
        @forelse($products as $product)
            <div class="product-row bg-white rounded-2xl shadow-sm border border-slate-100 p-4"
                 data-name="{{ strtolower($product->name) }}"
                 data-barcode="{{ strtolower($product->barcode ?? '') }}"
                 data-category="{{ $product->category_id ?? '' }}"
                 data-status="{{ $product->stock == 0 ? 'out' : ($product->isLowStock() ? 'low' : 'ok') }}">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-slate-800 truncate">{{ $product->name }}</p>
                        @if($product->barcode)
                            <p class="text-xs text-slate-400 font-mono mt-0.5">{{ $product->barcode }}</p>
                        @endif
                        @if($product->category)
                            <span class="inline-flex items-center gap-1 text-xs text-slate-500 mt-1">
                                <span class="w-2 h-2 rounded-full flex-shrink-0" style="background:{{ $product->category->color ?? '#6366f1' }}"></span>
                                {{ $product->category->name }}
                            </span>
                        @endif
                    </div>
                    <div class="text-right flex-shrink-0">
                        @if($product->stock == 0)
                            <span class="bg-red-100 text-red-700 px-2.5 py-1 rounded-full text-xs font-medium">Agotado</span>
                        @elseif($product->isLowStock())
                            <span class="bg-amber-100 text-amber-700 px-2.5 py-1 rounded-full text-xs font-medium">Stock bajo</span>
                        @else
                            <span class="bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded-full text-xs font-medium">OK</span>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-2 mt-3 pt-3 border-t border-slate-100">
                    <div>
                        <p class="text-xs text-slate-400">Precio</p>
                        <p class="font-semibold text-slate-800 text-sm">${{ number_format($product->price, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Costo</p>
                        <p class="text-slate-500 text-sm">${{ number_format($product->cost, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Stock</p>
                        <p class="font-bold text-sm {{ $product->stock == 0 ? 'text-red-600' : ($product->isLowStock() ? 'text-amber-600' : 'text-slate-700') }}">
                            {{ $product->stock }} <span class="text-slate-400 font-normal text-xs">/ {{ $product->min_stock }}</span>
                        </p>
                    </div>
                </div>

                <div class="flex gap-2 mt-3">
                    <a href="{{ route('store.products.edit', $product) }}"
                       class="flex-1 text-center text-indigo-600 text-sm py-2 rounded-xl bg-indigo-50 hover:bg-indigo-100 font-medium transition-colors">
                        ✏️ Editar
                    </a>
                    <button type="button"
                        onclick="openStockModal({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->stock }}, '{{ route('store.products.addStock', $product) }}')"
                        class="flex-1 text-center text-emerald-600 text-sm py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 font-medium transition-colors">
                        📦 + Stock
                    </button>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 px-4 py-12 text-center text-slate-400">
                No hay productos.
                <a href="{{ route('store.products.create') }}" class="text-indigo-600 hover:underline block mt-2">Agrega el primero →</a>
            </div>
        @endforelse
    </div>

    {{-- ══ VISTA ESCRITORIO: tabla ══ --}}
    <div class="hidden lg:block bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 uppercase text-xs border-b border-slate-100">
                <tr>
                    <th class="px-5 py-3.5 text-left">Producto</th>
                    <th class="px-5 py-3.5 text-left">Categoría</th>
                    <th class="px-5 py-3.5 text-right">Precio</th>
                    <th class="px-5 py-3.5 text-right">Costo</th>
                    <th class="px-5 py-3.5 text-right">Stock</th>
                    <th class="px-5 py-3.5 text-center">Estado</th>
                    <th class="px-5 py-3.5 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody id="productsTable" class="divide-y divide-slate-100">
                @forelse($products as $product)
                    <tr class="hover:bg-slate-50/70 product-row transition-colors"
                        data-name="{{ strtolower($product->name) }}"
                        data-barcode="{{ strtolower($product->barcode ?? '') }}"
                        data-category="{{ $product->category_id ?? '' }}"
                        data-status="{{ $product->stock == 0 ? 'out' : ($product->isLowStock() ? 'low' : 'ok') }}">
                        <td class="px-5 py-3.5">
                            <div class="font-medium text-slate-800">{{ $product->name }}</div>
                            @if($product->barcode)
                                <div class="text-slate-400 text-xs font-mono">{{ $product->barcode }}</div>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            @if($product->category)
                                <span class="inline-flex items-center gap-1.5 text-xs text-slate-600">
                                    <span class="w-2 h-2 rounded-full" style="background:{{ $product->category->color ?? '#6366f1' }}"></span>
                                    {{ $product->category->name }}
                                </span>
                            @else
                                <span class="text-slate-300">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-right font-semibold text-slate-800">${{ number_format($product->price, 0, ',', '.') }}</td>
                        <td class="px-5 py-3.5 text-right text-slate-400">${{ number_format($product->cost, 0, ',', '.') }}</td>
                        <td class="px-5 py-3.5 text-right">
                            <span class="font-bold {{ $product->stock == 0 ? 'text-red-600' : ($product->isLowStock() ? 'text-amber-600' : 'text-slate-700') }}">
                                {{ $product->stock }}
                            </span>
                            <span class="text-slate-400 text-xs">/ {{ $product->min_stock }}</span>
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            @if($product->stock == 0)
                                <span class="bg-red-100 text-red-700 px-2.5 py-1 rounded-full text-xs font-medium">Agotado</span>
                            @elseif($product->isLowStock())
                                <span class="bg-amber-100 text-amber-700 px-2.5 py-1 rounded-full text-xs font-medium">Stock bajo</span>
                            @else
                                <span class="bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded-full text-xs font-medium">OK</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('store.products.edit', $product) }}"
                                   class="text-indigo-600 hover:bg-indigo-50 text-xs px-3 py-1.5 rounded-lg transition-colors font-medium">
                                    ✏️ Editar
                                </a>
                                <button type="button"
                                    onclick="openStockModal({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->stock }}, '{{ route('store.products.addStock', $product) }}')"
                                    class="text-emerald-600 hover:bg-emerald-50 text-xs px-3 py-1.5 rounded-lg transition-colors font-medium">
                                    📦 + Stock
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center text-slate-400">
                            No hay productos.
                            <a href="{{ route('store.products.create') }}" class="text-indigo-600 hover:underline">Agrega el primero</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div id="noResults" class="hidden px-4 py-12 text-center text-slate-400">
            <p class="text-4xl mb-3">🔍</p>
            <p>No se encontraron productos con ese criterio.</p>
            <button onclick="clearFilters()" class="text-indigo-600 hover:underline text-sm mt-2">Limpiar búsqueda</button>
        </div>
    </div>

    {{-- Sin resultados móvil --}}
    <div id="noResultsMobile" class="hidden lg:hidden bg-white rounded-2xl p-12 text-center text-slate-400">
        <p class="text-4xl mb-3">🔍</p>
        <p>No se encontraron productos.</p>
        <button onclick="clearFilters()" class="text-indigo-600 hover:underline text-sm mt-2">Limpiar búsqueda</button>
    </div>

@endsection

@push('scripts')
<script>
function filterProducts() {
    const search   = document.getElementById('searchInput').value.toLowerCase();
    const category = document.getElementById('categoryFilter').value;
    const status   = document.getElementById('statusFilter').value;
    const rows     = document.querySelectorAll('.product-row');
    const clearBtn = document.getElementById('clearBtn');
    let visible = 0;

    rows.forEach(row => {
        const matchSearch   = !search   || row.dataset.name.includes(search) || row.dataset.barcode.includes(search);
        const matchCategory = !category || row.dataset.category === category;
        const matchStatus   = !status   || row.dataset.status === status;
        const show = matchSearch && matchCategory && matchStatus;
        row.classList.toggle('hidden', !show);
        if (show) visible++;
    });

    document.getElementById('resultCount').textContent = visible + ' producto(s)';
    document.getElementById('noResults')?.classList.toggle('hidden', visible > 0);
    document.getElementById('noResultsMobile')?.classList.toggle('hidden', visible > 0);
    clearBtn.classList.toggle('hidden', !search && !category && !status);
}

function clearFilters() {
    document.getElementById('searchInput').value    = '';
    document.getElementById('categoryFilter').value = '';
    document.getElementById('statusFilter').value   = '';
    filterProducts();
}

function toggleBarcodeScanner() {
    const scanner = document.getElementById('barcodeScanner');
    scanner.classList.toggle('hidden');
    if (!scanner.classList.contains('hidden')) {
        document.getElementById('barcodeInput').focus();
    }
}

function handleBarcode(e) {
    if (e.key === 'Enter') {
        const code = e.target.value.trim();
        if (code) {
            document.getElementById('searchInput').value = code;
            filterProducts();
            e.target.value = '';
            toggleBarcodeScanner();
        }
    }
}

function openStockModal(id, name, stock, url) {
    document.getElementById('modalProductName').textContent = name;
    document.getElementById('modalProductStock').textContent = 'Stock actual: ' + stock + ' unidades';
    document.getElementById('stockForm').action = url;
    document.getElementById('stockQty').value = 1;
    document.getElementById('stockModal').classList.remove('hidden');
    document.getElementById('stockQty').focus();
}

function closeStockModal() {
    document.getElementById('stockModal').classList.add('hidden');
}

function changeStockQty(delta) {
    const input = document.getElementById('stockQty');
    input.value = Math.max(1, parseInt(input.value || 1) + delta);
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeStockModal(); });
document.getElementById('stockModal').addEventListener('click', function(e) {
    if (e.target === this) closeStockModal();
});
</script>
@endpush
