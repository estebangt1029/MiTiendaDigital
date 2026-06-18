@extends('layouts.store')
@section('title', 'Inventario')
@section('page-title', 'Inventario')
@section('page-subtitle', 'Productos y stock de la tienda')
@section('header-actions')
    <a href="{{ route('store.products.create') }}"
       class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 font-medium">
        + Nuevo producto
    </a>
@endsection

@section('content')

    {{-- Alerta stock bajo --}}
    @if($lowStock > 0)
        <div class="bg-amber-50 border border-amber-200 text-amber-700 px-4 py-3 rounded-lg mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"/>
            </svg>
            <strong>{{ $lowStock }} producto(s)</strong> con stock bajo o agotado.
        </div>
    @endif

    {{-- Barra de búsqueda en tiempo real --}}
    <div class="bg-white rounded-xl shadow p-4 mb-4">
        <div class="flex gap-3 flex-wrap">
            <div class="flex-1 relative min-w-64">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" id="searchInput"
                    placeholder="Buscar por nombre o código de barras..."
                    class="w-full border rounded-lg pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    oninput="filterProducts()">
            </div>
            <select id="categoryFilter" onchange="filterProducts()"
                class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Todas las categorías</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
            <select id="statusFilter" onchange="filterProducts()"
                class="border rounded-lg px-3 py-2 text-sm focus:outline-none">
                <option value="">Todos los estados</option>
                <option value="ok">Stock OK</option>
                <option value="low">Stock bajo</option>
                <option value="out">Agotados</option>
            </select>
            {{-- Botón lector de barras --}}
            <button onclick="toggleBarcodeScanner()"
                class="border border-indigo-300 text-indigo-600 px-3 py-2 rounded-lg text-sm hover:bg-indigo-50 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                </svg>
                Escanear
            </button>
        </div>

        {{-- Input oculto para lector de barras --}}
        <div id="barcodeScanner" class="hidden mt-3">
            <div class="flex gap-2 items-center bg-indigo-50 border border-indigo-200 rounded-lg px-3 py-2">
                <svg class="w-4 h-4 text-indigo-500 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 4a1 1 0 011-1h1a1 1 0 010 2H3a1 1 0 01-1-1zm4 0a1 1 0 011-1h1a1 1 0 010 2H7a1 1 0 01-1-1zm5-1a1 1 0 000 2h1a1 1 0 000-2h-1zm4 0a1 1 0 000 2h1a1 1 0 000-2h-1zM2 8a1 1 0 011-1h1a1 1 0 010 2H3a1 1 0 01-1-1zm13-1a1 1 0 000 2h1a1 1 0 000-2h-1zM2 12a1 1 0 011-1h1a1 1 0 010 2H3a1 1 0 01-1-1zm13-1a1 1 0 000 2h1a1 1 0 000-2h-1z"/>
                </svg>
                <input type="text" id="barcodeInput" autofocus
                    placeholder="Escanea el código de barras..."
                    class="flex-1 bg-transparent text-sm focus:outline-none text-indigo-700"
                    onkeydown="handleBarcode(event)">
                <button onclick="toggleBarcodeScanner()" class="text-indigo-400 hover:text-indigo-600 text-xs">✕ Cerrar</button>
            </div>
        </div>

        {{-- Contador de resultados --}}
        <div class="mt-2 flex justify-between items-center">
            <p id="resultCount" class="text-xs text-gray-400">{{ $products->count() }} productos</p>
            <button onclick="clearFilters()" class="text-xs text-indigo-500 hover:underline hidden" id="clearBtn">
                Limpiar filtros
            </button>
        </div>
    </div>

    {{-- Modal para agregar stock --}}
    <div id="stockModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-sm">
            <h3 class="font-bold text-lg mb-1" id="modalProductName"></h3>
            <p class="text-gray-400 text-sm mb-4" id="modalProductStock"></p>
            <form method="POST" id="stockForm">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Cantidad a agregar</label>
                    <div class="flex items-center gap-3">
                        <button type="button" onclick="changeStockQty(-1)"
                            class="w-10 h-10 rounded-xl border-2 text-xl font-bold hover:bg-gray-100 flex items-center justify-center">−</button>
                        <input type="number" name="quantity" id="stockQty" value="1" min="1"
                            class="flex-1 border-2 rounded-xl px-3 py-2 text-center text-lg font-bold focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <button type="button" onclick="changeStockQty(1)"
                            class="w-10 h-10 rounded-xl border-2 text-xl font-bold hover:bg-gray-100 flex items-center justify-center">+</button>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Nota (opcional)</label>
                    <input type="text" name="note" placeholder="Ej: Llegó pedido proveedor"
                        class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="flex gap-3">
                    <button type="submit"
                        class="flex-1 bg-indigo-600 text-white py-2.5 rounded-xl font-semibold hover:bg-indigo-700">
                        Agregar stock
                    </button>
                    <button type="button" onclick="closeStockModal()"
                        class="flex-1 border py-2.5 rounded-xl text-gray-600 hover:bg-gray-50">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabla de productos --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">Producto</th>
                    <th class="px-4 py-3 text-left">Categoría</th>
                    <th class="px-4 py-3 text-right">Precio</th>
                    <th class="px-4 py-3 text-right">Costo</th>
                    <th class="px-4 py-3 text-right">Stock</th>
                    <th class="px-4 py-3 text-center">Estado</th>
                    <th class="px-4 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody id="productsTable" class="divide-y divide-gray-100">
                @forelse($products as $product)
                    <tr class="hover:bg-gray-50 product-row"
                        data-name="{{ strtolower($product->name) }}"
                        data-barcode="{{ strtolower($product->barcode ?? '') }}"
                        data-category="{{ $product->category_id ?? '' }}"
                        data-status="{{ $product->stock == 0 ? 'out' : ($product->isLowStock() ? 'low' : 'ok') }}">
                        <td class="px-4 py-3">
                            <div class="font-medium">{{ $product->name }}</div>
                            @if($product->barcode)
                                <div class="text-gray-400 text-xs font-mono">{{ $product->barcode }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($product->category)
                                <span class="inline-flex items-center gap-1 text-xs">
                                    <span class="w-2 h-2 rounded-full" style="background:{{ $product->category->color ?? '#6366f1' }}"></span>
                                    {{ $product->category->name }}
                                </span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right font-medium">${{ number_format($product->price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-gray-400">${{ number_format($product->cost, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right">
                            <span class="font-bold {{ $product->stock == 0 ? 'text-red-600' : ($product->isLowStock() ? 'text-amber-600' : 'text-gray-700') }}">
                                {{ $product->stock }}
                            </span>
                            <span class="text-gray-400 text-xs">/ {{ $product->min_stock }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($product->stock == 0)
                                <span class="bg-red-100 text-red-700 px-2 py-0.5 rounded-full text-xs">Agotado</span>
                            @elseif($product->isLowStock())
                                <span class="bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full text-xs">Stock bajo</span>
                            @else
                                <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-xs">OK</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('store.products.edit', $product) }}"
                                   class="text-indigo-600 hover:underline text-xs px-2 py-1 rounded hover:bg-indigo-50">
                                    ✏️ Editar
                                </a>
                                <button type="button"
                                    onclick="openStockModal({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->stock }}, '{{ route('store.products.addStock', $product) }}')"
                                    class="text-green-600 hover:underline text-xs px-2 py-1 rounded hover:bg-green-50">
                                    📦 + Stock
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-gray-400">
                            No hay productos.
                            <a href="{{ route('store.products.create') }}" class="text-indigo-600 hover:underline">Agrega el primero</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Mensaje sin resultados --}}
        <div id="noResults" class="hidden px-4 py-10 text-center text-gray-400">
            <p class="text-3xl mb-2">🔍</p>
            <p>No se encontraron productos con ese criterio.</p>
            <button onclick="clearFilters()" class="text-indigo-600 hover:underline text-sm mt-2">Limpiar búsqueda</button>
        </div>
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

        if (matchSearch && matchCategory && matchStatus) {
            row.classList.remove('hidden');
            visible++;
        } else {
            row.classList.add('hidden');
        }
    });

    document.getElementById('resultCount').textContent = visible + ' producto(s)';
    document.getElementById('noResults').classList.toggle('hidden', visible > 0);
    clearBtn.classList.toggle('hidden', !search && !category && !status);
}

function clearFilters() {
    document.getElementById('searchInput').value    = '';
    document.getElementById('categoryFilter').value = '';
    document.getElementById('statusFilter').value   = '';
    filterProducts();
}

// ── Lector de barras ──────────────────────────────────────
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

// ── Modal de stock ────────────────────────────────────────
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
    const val   = parseInt(input.value || 1) + delta;
    input.value = Math.max(1, val);
}

// Cerrar modal con Escape
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeStockModal();
});

// Cerrar modal al hacer clic fuera
document.getElementById('stockModal').addEventListener('click', function(e) {
    if (e.target === this) closeStockModal();
});
</script>
@endpush