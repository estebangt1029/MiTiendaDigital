@extends('layouts.store')
@section('title', 'Nueva Venta')
@section('page-title', 'Nueva venta')
@section('page-subtitle', 'Registra productos rápidamente')

@section('content')
<div class="max-w-5xl pb-10 px-2">
    <form method="POST" action="{{ route('store.sales.store') }}" id="saleForm">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            {{-- Columna izquierda: productos --}}
            <div class="md:col-span-2 space-y-4">

                {{-- Buscador de productos --}}
                <div class="bg-white rounded-xl shadow p-4">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-semibold">🔍 Buscar producto</label>
                        <div class="flex gap-2">

    <button
        type="button"
        onclick="startScanner('single')"
        class="flex items-center gap-1.5 bg-indigo-600 text-white text-xs px-3 py-1.5 rounded-lg hover:bg-indigo-700">

        📷 Escanear

    </button>

    <button
        type="button"
        onclick="startScanner('continuous')"
        class="flex items-center gap-1.5 bg-emerald-600 text-white text-xs px-3 py-1.5 rounded-lg hover:bg-emerald-700">

        📦 Continuo

    </button>

</div>
                    </div>
                    <div class="relative">
                        <input type="text" id="productSearch"
                            placeholder="Escribe el nombre o escanea el código de barras..."
                            autocomplete="off"
                            class="w-full border-2 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-indigo-500 focus:ring-0"
                            oninput="searchProducts(this.value)"
                            onkeydown="handleSearchKey(event)">
                        <div id="productResults"
                             class="absolute z-20 w-full bg-white border rounded-xl shadow-xl mt-1 hidden max-h-64 overflow-y-auto">
                        </div>
                    </div>
                </div>

                {{-- Lista de productos agregados --}}
                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <div class="px-4 py-3 border-b flex justify-between items-center">
                        <h3 class="font-semibold text-sm">Productos en la venta</h3>
                        <span id="itemCount" class="text-xs text-gray-400">0 productos</span>
                    </div>

                    <div id="itemsContainer" class="divide-y divide-gray-100">
                        {{-- Los productos se agregan aquí dinámicamente --}}
                    </div>

                    <div id="emptyMsg" class="px-4 py-10 text-center text-gray-400 text-sm">
                        <p class="text-3xl mb-2">🛒</p>
                        <p>Busca, escanea o agrega productos arriba</p>
                    </div>
                </div>
            </div>

            {{-- Columna derecha: resumen y pago --}}
            <div class="space-y-4">

                {{-- Cliente --}}
                <div class="bg-white rounded-xl shadow p-4">
                    <label class="block text-sm font-semibold mb-2">👤 Cliente</label>
                    <div class="relative">
                        <input type="text" id="customerSearch"
                            placeholder="Buscar cliente..."
                            autocomplete="off"
                            class="w-full border-2 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-indigo-500"
                            oninput="searchCustomers(this.value)">
                        <div id="customerResults"
                             class="absolute z-20 w-full bg-white border rounded-xl shadow-xl mt-1 hidden max-h-48 overflow-y-auto">
                        </div>
                    </div>
                    <input type="hidden" name="customer_id" id="customerId">
                    <div id="selectedCustomer" class="hidden mt-2 bg-indigo-50 rounded-lg px-3 py-2 text-sm flex justify-between items-center">
                        <span id="selectedCustomerName" class="font-medium text-indigo-700"></span>
                        <button type="button" onclick="clearCustomer()" class="text-indigo-400 hover:text-indigo-600 text-xs">✕</button>
                    </div>
                    <p id="noCustomerMsg" class="text-xs text-gray-400 mt-2">Cliente general (sin seleccionar)</p>
                </div>

                {{-- Tipo de venta --}}
                <div class="bg-white rounded-xl shadow p-4">
                    <label class="block text-sm font-semibold mb-2">Tipo de venta</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center justify-center gap-2 border-2 rounded-xl py-2 cursor-pointer transition-all"
                               id="typeContado">
                            <input type="radio" name="type" value="contado" checked class="hidden"
                                   onchange="selectType('contado')">
                            <span class="text-sm font-medium">💵 Contado</span>
                        </label>
                        <label class="flex items-center justify-center gap-2 border-2 rounded-xl py-2 cursor-pointer transition-all"
                               id="typeFiado">
                            <input type="radio" name="type" value="fiado" class="hidden"
                                   onchange="selectType('fiado')">
                            <span class="text-sm font-medium">📋 Fiado</span>
                        </label>
                    </div>
                    <div class="mt-3">
                        <label class="block text-xs text-gray-500 mb-1">Método de pago</label>
                        <select name="payment_method"
                            class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="efectivo">💵 Efectivo</option>
                            <option value="nequi">📱 Nequi</option>
                            <option value="daviplata">📱 Daviplata</option>
                            <option value="transferencia">🏦 Transferencia</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                </div>

                {{-- Resumen de pago --}}
                <div class="bg-white rounded-xl shadow p-4 sticky top-20">
                    <h3 class="font-semibold mb-3 text-sm">💰 Resumen</h3>

                    <div class="space-y-2 text-sm mb-4">
                        <div class="flex justify-between text-gray-500">
                            <span>Subtotal</span>
                            <span id="subtotalDisplay">$0</span>
                        </div>
                        <div class="flex justify-between font-bold text-lg border-t pt-2">
                            <span>Total</span>
                            <span id="totalDisplay" class="text-indigo-600">$0</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="block text-xs text-gray-500 mb-1">Recibido del cliente</label>
                        <input type="number" name="paid" id="paidInput"
                               value="0" min="0" step="100"
                               oninput="updateChange()"
                               class="w-full border-2 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-green-500 font-bold text-lg text-center">
                    </div>

                    <div class="grid grid-cols-2 gap-2 mb-4">
                        <div class="bg-red-50 rounded-lg p-2 text-center">
                            <p class="text-xs text-red-400">Deuda</p>
                            <p id="debtDisplay" class="font-bold text-red-600">$0</p>
                        </div>
                        <div class="bg-green-50 rounded-lg p-2 text-center">
                            <p class="text-xs text-green-400">Devolver</p>
                            <p id="changeDisplay" class="font-bold text-green-600">$0</p>
                        </div>
                    </div>

                    {{-- Botones de monto rápido --}}
                    <div class="grid grid-cols-3 gap-1 mb-4">
                        @foreach([1000, 2000, 5000, 10000, 20000, 50000] as $amount)
                            <button type="button"
                                    onclick="setQuickAmount({{ $amount }})"
                                    class="text-xs border rounded-lg py-1.5 hover:bg-indigo-50 hover:border-indigo-300 transition-all text-gray-600">
                                ${{ number_format($amount, 0, ',', '.') }}
                            </button>
                        @endforeach
                    </div>

                    <div class="mb-3">
                        <input type="text" name="notes" placeholder="Nota (opcional)"
                            class="w-full border rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    </div>

                    <button type="submit" id="submitBtn" disabled
                        class="w-full bg-indigo-600 text-white py-3 rounded-xl font-bold hover:bg-indigo-700 disabled:opacity-40 disabled:cursor-not-allowed transition-all">
                        Registrar venta
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- ══ MODAL: Escáner de cámara ══ --}}
<div id="cameraModal" class="hidden fixed inset-0 bg-black z-50 flex flex-col">
    <div class="flex items-center justify-between px-4 py-3 bg-black/80 text-white">
        <p class="font-medium text-sm">📷 Apunta al código de barras</p>
        <button type="button" onclick="closeCameraScanner()" class="p-2 hover:bg-white/10 rounded-lg">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
    <div id="cameraReader" class="flex-1"></div>
    <div id="cameraStatus" class="px-4 py-3 bg-black/80 text-center text-white text-sm min-h-[48px] flex items-center justify-center">
        Iniciando cámara...
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
const products  = @json($products);
const customers = @json($customers);
let cart        = {}; // { product_id: { product, quantity } }
let total       = 0;

function formatCOP(n) {
    return '$' + Math.round(n).toLocaleString('es-CO');
}

// ── Buscador de productos ──────────────────────────────────
function searchProducts(query) {
    const results = document.getElementById('productResults');
    if (query.length < 1) { results.classList.add('hidden'); return; }

    const q      = query.toLowerCase();
    const found  = products.filter(p =>
        p.name.toLowerCase().includes(q) ||
        (p.barcode && p.barcode.includes(q))
    ).slice(0, 10);

    if (!found.length) {
        results.innerHTML = '<p class="px-4 py-3 text-sm text-gray-400">No se encontraron productos</p>';
        results.classList.remove('hidden');
        return;
    }

    results.innerHTML = found.map((p, i) => `
        <div class="px-4 py-3 hover:bg-indigo-50 cursor-pointer flex justify-between items-center border-b last:border-0 product-option"
             data-index="${i}"
             onclick="addToCart(${p.id})">
            <div>
                <p class="font-medium text-sm">${p.name}</p>
                <p class="text-xs text-gray-400">${p.barcode ?? 'Sin código'} · Stock: ${p.stock}</p>
            </div>
            <div class="text-right">
                <p class="font-bold text-indigo-600 text-sm">${formatCOP(p.price)}</p>
                ${p.stock === 0 ? '<span class="text-xs text-red-400">Agotado</span>' : ''}
            </div>
        </div>
    `).join('');

    results.classList.remove('hidden');
}

function handleSearchKey(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        const firstResult = document.querySelector('.product-option');
        if (firstResult) firstResult.click();
    }
    if (e.key === 'Escape') {
        document.getElementById('productResults').classList.add('hidden');
    }
}

function addToCart(productId) {
    const product = products.find(p => p.id === productId);
    if (!product || product.stock === 0) return;

    if (cart[productId]) {
        if (cart[productId].quantity >= product.stock) {
            alert(`Solo hay ${product.stock} unidades disponibles`);
            return;
        }
        cart[productId].quantity++;
    } else {
        cart[productId] = { product, quantity: 1 };
    }

    document.getElementById('productSearch').value = '';
    document.getElementById('productResults').classList.add('hidden');
    renderCart();
}

function updateQuantity(productId, qty) {
    qty = parseInt(qty);
    const product = products.find(p => p.id === productId);
    if (qty <= 0) {
        removeFromCart(productId);
        return;
    }
    if (qty > product.stock) {
        alert(`Solo hay ${product.stock} unidades disponibles`);
        document.getElementById(`qty_${productId}`).value = cart[productId].quantity;
        return;
    }
    cart[productId].quantity = qty;
    renderCart();
}

function removeFromCart(productId) {
    delete cart[productId];
    renderCart();
}

function renderCart() {
    const container = document.getElementById('itemsContainer');
    const emptyMsg  = document.getElementById('emptyMsg');
    const items     = Object.values(cart);

    document.getElementById('itemCount').textContent = items.length + ' producto(s)';

    if (!items.length) {
        container.innerHTML = '';
        emptyMsg.classList.remove('hidden');
        updateTotal(0);
        return;
    }

    emptyMsg.classList.add('hidden');
    let subtotal = 0;
    let inputsHtml = '';

    container.innerHTML = items.map((item, idx) => {
        const sub = item.product.price * item.quantity;
        subtotal += sub;
        inputsHtml += `
            <input type="hidden" name="items[${idx}][product_id]" value="${item.product.id}">
            <input type="hidden" name="items[${idx}][quantity]" value="${item.quantity}" id="hiddenQty_${item.product.id}">
        `;
        return `
            <div class="px-4 py-3 flex items-center gap-3">
                <div class="flex-1">
                    <p class="font-medium text-sm">${item.product.name}</p>
                    <p class="text-xs text-gray-400">${formatCOP(item.product.price)} c/u</p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="updateQuantity(${item.product.id}, ${item.quantity - 1})"
                            class="w-7 h-7 rounded-lg border flex items-center justify-center hover:bg-gray-100 text-lg font-bold text-gray-500">−</button>
                    <input type="number" id="qty_${item.product.id}"
                           value="${item.quantity}" min="1" max="${item.product.stock}"
                           onchange="updateQuantity(${item.product.id}, this.value)"
                           class="w-12 text-center border rounded-lg py-1 text-sm font-bold focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <button type="button" onclick="updateQuantity(${item.product.id}, ${item.quantity + 1})"
                            class="w-7 h-7 rounded-lg border flex items-center justify-center hover:bg-gray-100 text-lg font-bold text-gray-500">+</button>
                </div>
                <div class="w-24 text-right">
                    <p class="font-bold text-sm text-indigo-600">${formatCOP(sub)}</p>
                </div>
                <button type="button" onclick="removeFromCart(${item.product.id})"
                        class="text-red-400 hover:text-red-600 text-lg font-bold w-6">×</button>
            </div>
        `;
    }).join('');

    // Agregar inputs hidden al final del form
    let hiddenContainer = document.getElementById('hiddenInputs');
    if (!hiddenContainer) {
        hiddenContainer = document.createElement('div');
        hiddenContainer.id = 'hiddenInputs';
        document.getElementById('saleForm').appendChild(hiddenContainer);
    }
    hiddenContainer.innerHTML = inputsHtml;

    updateTotal(subtotal);
}

function updateTotal(subtotal) {
    total = subtotal;
    document.getElementById('subtotalDisplay').textContent = formatCOP(subtotal);
    document.getElementById('totalDisplay').textContent    = formatCOP(subtotal);
    document.getElementById('submitBtn').disabled = subtotal === 0;
    updateChange();
}

function updateChange() {
    const paid   = parseFloat(document.getElementById('paidInput').value || 0);
    const debt   = Math.max(0, total - paid);
    const change = Math.max(0, paid - total);
    document.getElementById('debtDisplay').textContent   = formatCOP(debt);
    document.getElementById('changeDisplay').textContent = formatCOP(change);
}

function setQuickAmount(amount) {
    document.getElementById('paidInput').value = amount;
    updateChange();
}

// ── Buscador de clientes ───────────────────────────────────
function searchCustomers(query) {
    const results = document.getElementById('customerResults');
    if (query.length < 1) { results.classList.add('hidden'); return; }

    const q     = query.toLowerCase();
    const found = customers.filter(c =>
        c.name.toLowerCase().includes(q) ||
        (c.phone && c.phone.includes(q))
    ).slice(0, 8);

    if (!found.length) {
        results.innerHTML = '<p class="px-4 py-3 text-sm text-gray-400">No se encontraron clientes</p>';
        results.classList.remove('hidden');
        return;
    }

    results.innerHTML = found.map(c => `
        <div class="px-4 py-3 hover:bg-indigo-50 cursor-pointer flex justify-between items-center border-b last:border-0"
             onclick="selectCustomer(${c.id}, '${c.name.replace(/'/g, "\\'")}', ${c.total_debt})">
            <div>
                <p class="font-medium text-sm">${c.name}</p>
                <p class="text-xs text-gray-400">${c.phone ?? 'Sin teléfono'}</p>
            </div>
            ${c.total_debt > 0 ? `<span class="text-xs text-red-500 font-medium">Deuda: ${formatCOP(c.total_debt)}</span>` : '<span class="text-xs text-green-500">Al día</span>'}
        </div>
    `).join('');

    results.classList.remove('hidden');
}

function selectCustomer(id, name, debt) {
    document.getElementById('customerId').value       = id;
    document.getElementById('customerSearch').value   = '';
    document.getElementById('customerResults').classList.add('hidden');
    document.getElementById('selectedCustomerName').textContent = name + (debt > 0 ? ` · Deuda: ${formatCOP(debt)}` : '');
    document.getElementById('selectedCustomer').classList.remove('hidden');
    document.getElementById('noCustomerMsg').classList.add('hidden');
}

function clearCustomer() {
    document.getElementById('customerId').value = '';
    document.getElementById('selectedCustomer').classList.add('hidden');
    document.getElementById('noCustomerMsg').classList.remove('hidden');
}

// ── Tipo de venta ──────────────────────────────────────────
function selectType(type) {
    const contado = document.getElementById('typeContado');
    const fiado   = document.getElementById('typeFiado');
    if (type === 'contado') {
        contado.classList.add('border-indigo-500', 'bg-indigo-50');
        contado.classList.remove('border-gray-200');
        fiado.classList.remove('border-indigo-500', 'bg-indigo-50');
        fiado.classList.add('border-gray-200');
    } else {
        fiado.classList.add('border-indigo-500', 'bg-indigo-50');
        fiado.classList.remove('border-gray-200');
        contado.classList.remove('border-indigo-500', 'bg-indigo-50');
        contado.classList.add('border-gray-200');
    }
}

// Cerrar dropdowns al hacer clic fuera
document.addEventListener('click', e => {
    if (!e.target.closest('#productSearch') && !e.target.closest('#productResults')) {
        document.getElementById('productResults').classList.add('hidden');
    }
    if (!e.target.closest('#customerSearch') && !e.target.closest('#customerResults')) {
        document.getElementById('customerResults').classList.add('hidden');
    }
});

// ══════════════════════════════════════════════════════════
// Escáner de código de barras con cámara (html5-qrcode)
// ══════════════════════════════════════════════════════════
let html5QrCode = null;
let scannerRunning = false;

// Evita múltiples lecturas del mismo código
let lastScannedCode = null;
let lastScanTime = 0;

// single = cierra después de leer
// continuous = sigue leyendo
let scanMode = 'single';

function startScanner(mode = 'single') {

    scanMode = mode;

    lastScannedCode = null;
    lastScanTime = 0;

    openCameraScanner();
}

function openCameraScanner() {
    document.getElementById('cameraModal').classList.remove('hidden');
    document.getElementById('cameraStatus').textContent = 'Iniciando cámara...';

    html5QrCode = new Html5Qrcode('cameraReader');

    const config = {
        fps: 10,
        qrbox: { width: 280, height: 140 },
        formatsToSupport: [
            Html5QrcodeSupportedFormats.EAN_13,
            Html5QrcodeSupportedFormats.EAN_8,
            Html5QrcodeSupportedFormats.UPC_A,
            Html5QrcodeSupportedFormats.UPC_E,
            Html5QrcodeSupportedFormats.CODE_128,
        ],
    };

    html5QrCode.start(
        { facingMode: 'environment' },
        config,
        onScanSuccess,
        () => { /* errores de frame, normales mientras enfoca */ }
    ).then(() => {
        scannerRunning = true;
        document.getElementById('cameraStatus').textContent = 'Apunta al código de barras del producto';
    }).catch(err => {
        document.getElementById('cameraStatus').textContent =
            '⚠ No se pudo acceder a la cámara. Revisa los permisos del navegador.';
        console.error('Error cámara:', err);
    });
}

function onScanSuccess(decodedText) {

    if (!scannerRunning) return;

    const barcode = decodedText.trim();
    const now = Date.now();

    // Evitar lecturas repetidas
    if (
        barcode === lastScannedCode &&
        now - lastScanTime < 2000
    ) {
        return;
    }

    lastScannedCode = barcode;
    lastScanTime = now;

    if (navigator.vibrate) {
        navigator.vibrate(100);
    }

    const product = products.find(
        p => p.barcode && p.barcode.trim() === barcode
    );

    if (product) {

        addToCart(product.id);

        document.getElementById('cameraStatus').textContent =
            '✓ ' + product.name + ' agregado';

        if (scanMode === 'single') {

            setTimeout(() => {
                closeCameraScanner();
            }, 500);

        } else {

            setTimeout(() => {
                document.getElementById('cameraStatus').textContent =
                    'Apunta al siguiente producto';
            }, 1000);

        }

    } else {

        document.getElementById('cameraStatus').textContent =
            '⚠ Código no encontrado';

        setTimeout(() => {

            closeCameraScanner();

            document.getElementById('productSearch').value = barcode;

            searchProducts(barcode);

            alert(
                'El producto no existe.\n\nCódigo: ' +
                barcode +
                '\n\nDebes crearlo en inventario.'
            );

        }, 1000);
    }
}

function closeCameraScanner() {
    document.getElementById('cameraModal').classList.add('hidden');
    if (html5QrCode && scannerRunning) {
        html5QrCode.stop().then(() => {
            html5QrCode.clear();
            scannerRunning = false;

            lastScannedCode = null;
lastScanTime = 0;
        }).catch(() => {
            scannerRunning = false;
        });
    }
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeCameraScanner();
});

// Inicializar
selectType('contado');
</script>
@endpush