@extends('layouts.store')
@section('title', 'Nueva Compra')
@section('page-title', 'Nueva compra')
@section('page-subtitle', 'Registra una compra a proveedor y actualiza tu stock')

@section('content')
    <form method="POST" action="{{ route('store.purchases.store') }}" id="purchaseForm">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- Columna izquierda: selección de productos --}}
            <div class="lg:col-span-2 space-y-4">

                {{-- Buscador de productos --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Agregar producto</label>
                    <div class="relative">
                        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" id="productSearch"
                            placeholder="Buscar producto por nombre..."
                            class="w-full border border-slate-200 rounded-xl pl-9 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-slate-50"
                            autocomplete="off">
                    </div>
                    <div id="productResults" class="hidden mt-2 border border-slate-200 rounded-xl max-h-64 overflow-y-auto"></div>
                </div>

                {{-- Lista de productos agregados --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4">
                    <h3 class="font-semibold text-slate-800 mb-3">Productos en esta compra</h3>
                    <div id="itemsList" class="space-y-2">
                        <p id="emptyItems" class="text-center text-slate-400 py-8 text-sm">
                            Aún no has agregado productos. Búscalos arriba.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Columna derecha: proveedor y resumen de pago --}}
            <div class="space-y-4">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Proveedor</label>
                    <select name="supplier_id" id="supplierSelect"
                        class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-slate-50">
                        <option value="">Sin proveedor (compra general)</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                    @error('supplier_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    @if($suppliers->isEmpty())
                        <p class="text-xs text-slate-400 mt-2">
                            No tienes proveedores aún.
                            <a href="{{ route('store.suppliers.create') }}" class="text-indigo-600 hover:underline">Crea uno →</a>
                        </p>
                    @endif
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Tipo de compra</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="contado" checked class="peer sr-only" onchange="togglePaymentType()">
                            <div class="text-center py-2.5 rounded-xl border-2 border-slate-200 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 text-sm font-medium transition-colors">
                                Contado
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="credito" class="peer sr-only" onchange="togglePaymentType()">
                            <div class="text-center py-2.5 rounded-xl border-2 border-slate-200 peer-checked:border-amber-500 peer-checked:bg-amber-50 peer-checked:text-amber-700 text-sm font-medium transition-colors">
                                Crédito
                            </div>
                        </label>
                    </div>
                    <p id="creditWarning" class="hidden text-xs text-amber-600 mt-2">
                        ⚠ Las compras a crédito requieren seleccionar un proveedor.
                    </p>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4">
                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="checkbox" name="update_product_cost" value="1" checked
                            class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm text-slate-700">Actualizar el costo de los productos con esta compra</span>
                    </label>
                    <p class="text-xs text-slate-400 mt-1.5 ml-6">
                        Si lo desmarcas, solo se sumará el stock sin cambiar el costo registrado.
                    </p>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4">
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-sm text-slate-500">Total de la compra</span>
                        <span id="totalDisplay" class="text-xl font-bold text-slate-800">$0</span>
                    </div>

                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Monto pagado ahora</label>
                    <input type="number" name="paid" id="paidInput" min="0" step="1" value="0" required
                        class="w-full border-2 border-slate-200 rounded-xl px-3 py-2.5 text-lg font-bold focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <button type="button" onclick="payFull()" class="text-xs text-indigo-500 hover:underline mt-1.5">
                        Pagar el total
                    </button>

                    <div class="mt-3 pt-3 border-t border-slate-100 flex justify-between text-sm">
                        <span class="text-slate-500">Quedará debiendo</span>
                        <span id="debtDisplay" class="font-bold text-red-500">$0</span>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Notas (opcional)</label>
                    <textarea name="notes" rows="2"
                        placeholder="Ej: Factura #4521"
                        class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-slate-50"></textarea>
                </div>

                @error('stock')
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">{{ $message }}</div>
                @enderror

                <button type="submit" id="submitBtn" disabled
                    class="w-full bg-indigo-600 text-white py-3.5 rounded-xl font-semibold hover:bg-indigo-700 transition-colors shadow-sm disabled:opacity-40 disabled:cursor-not-allowed">
                    Registrar compra
                </button>
                <a href="{{ route('store.purchases.index') }}" class="block text-center text-slate-500 text-sm py-2 hover:underline">
                    Cancelar
                </a>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
const allProducts = @json($products->map(fn($p) => [
    'id'    => $p->id,
    'name'  => $p->name,
    'cost'  => (float) $p->cost,
    'stock' => $p->stock,
    'category' => $p->category->name ?? null,
]));

let items = {}; // { productId: { name, quantity, unit_cost } }

const searchInput   = document.getElementById('productSearch');
const resultsBox    = document.getElementById('productResults');
const itemsList     = document.getElementById('itemsList');
const emptyItems    = document.getElementById('emptyItems');
const totalDisplay  = document.getElementById('totalDisplay');
const paidInput     = document.getElementById('paidInput');
const debtDisplay   = document.getElementById('debtDisplay');
const submitBtn     = document.getElementById('submitBtn');
const purchaseForm  = document.getElementById('purchaseForm');

searchInput.addEventListener('input', () => {
    const q = searchInput.value.trim().toLowerCase();
    if (!q) { resultsBox.classList.add('hidden'); resultsBox.innerHTML = ''; return; }

    const matches = allProducts.filter(p => p.name.toLowerCase().includes(q)).slice(0, 8);
    if (matches.length === 0) {
        resultsBox.innerHTML = '<p class="text-sm text-slate-400 px-3 py-3">Sin resultados.</p>';
        resultsBox.classList.remove('hidden');
        return;
    }

    resultsBox.innerHTML = matches.map(p => `
        <button type="button" onclick="addItem(${p.id})"
            class="w-full text-left px-3 py-2.5 hover:bg-indigo-50 border-b border-slate-100 last:border-0 flex justify-between items-center transition-colors">
            <div>
                <p class="text-sm font-medium text-slate-800">${p.name}</p>
                <p class="text-xs text-slate-400">Stock actual: ${p.stock} · Costo: $${p.cost.toLocaleString('es-CO')}</p>
            </div>
            <span class="text-indigo-600 text-xs font-semibold">+ Agregar</span>
        </button>
    `).join('');
    resultsBox.classList.remove('hidden');
});

function addItem(productId) {
    const product = allProducts.find(p => p.id === productId);
    if (!product) return;

    if (items[productId]) {
        items[productId].quantity += 1;
    } else {
        items[productId] = { name: product.name, quantity: 1, unit_cost: product.cost || 0 };
    }

    searchInput.value = '';
    resultsBox.classList.add('hidden');
    renderItems();
}

function removeItem(productId) {
    delete items[productId];
    renderItems();
}

function updateQty(productId, value) {
    const qty = Math.max(1, parseInt(value) || 1);
    items[productId].quantity = qty;
    renderItems();
}

function updateCost(productId, value) {
    const cost = Math.max(0, parseFloat(value) || 0);
    items[productId].unit_cost = cost;
    renderItems();
}

function renderItems() {
    const ids = Object.keys(items);

    if (ids.length === 0) {
        itemsList.innerHTML = '';
        itemsList.appendChild(emptyItems);
        emptyItems.classList.remove('hidden');
        updateTotals();
        return;
    }

    emptyItems.classList.add('hidden');

    itemsList.innerHTML = ids.map(id => {
        const item = items[id];
        const subtotal = item.quantity * item.unit_cost;
        return `
            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-800 truncate">${item.name}</p>
                    <p class="text-xs text-slate-400">Subtotal: $${subtotal.toLocaleString('es-CO')}</p>
                </div>
                <input type="number" min="1" value="${item.quantity}"
                    onchange="updateQty(${id}, this.value)"
                    class="w-16 border border-slate-200 rounded-lg px-2 py-1.5 text-sm text-center focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <input type="number" min="0" step="100" value="${item.unit_cost}"
                    onchange="updateCost(${id}, this.value)"
                    class="w-24 border border-slate-200 rounded-lg px-2 py-1.5 text-sm text-center focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    title="Costo unitario">
                <button type="button" onclick="removeItem(${id})" class="text-red-400 hover:text-red-600 p-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>

            {{-- Inputs ocultos para el submit del formulario --}}
            <input type="hidden" name="items[${id}][product_id]" value="${id}">
            <input type="hidden" name="items[${id}][quantity]" value="${item.quantity}">
            <input type="hidden" name="items[${id}][unit_cost]" value="${item.unit_cost}">
        `;
    }).join('');

    updateTotals();
}

function updateTotals() {
    const total = Object.values(items).reduce((sum, i) => sum + (i.quantity * i.unit_cost), 0);
    totalDisplay.textContent = '$' + total.toLocaleString('es-CO');

    const paid = parseFloat(paidInput.value) || 0;
    const debt = Math.max(0, total - paid);
    debtDisplay.textContent = '$' + debt.toLocaleString('es-CO');

    submitBtn.disabled = Object.keys(items).length === 0;
}

paidInput.addEventListener('input', updateTotals);

function payFull() {
    const total = Object.values(items).reduce((sum, i) => sum + (i.quantity * i.unit_cost), 0);
    paidInput.value = total;
    updateTotals();
}

function togglePaymentType() {
    const type = document.querySelector('input[name="type"]:checked').value;
    const supplier = document.getElementById('supplierSelect').value;
    const warning = document.getElementById('creditWarning');

    if (type === 'credito' && !supplier) {
        warning.classList.remove('hidden');
    } else {
        warning.classList.add('hidden');
    }
}

// Cerrar resultados al hacer clic fuera
document.addEventListener('click', (e) => {
    if (!searchInput.contains(e.target) && !resultsBox.contains(e.target)) {
        resultsBox.classList.add('hidden');
    }
});

purchaseForm.addEventListener('submit', (e) => {
    if (Object.keys(items).length === 0) {
        e.preventDefault();
        alert('Agrega al menos un producto a la compra.');
    }
});
</script>
@endpush