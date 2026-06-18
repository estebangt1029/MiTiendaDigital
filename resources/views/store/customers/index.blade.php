@extends('layouts.store')
@section('title', 'Clientes')
@section('page-title', 'Clientes')
@section('page-subtitle', 'Gestión de clientes y deudas')
@section('header-actions')
    <a href="{{ route('store.customers.create') }}"
       class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 font-medium">
        + Nuevo cliente
    </a>
@endsection

@section('content')
    {{-- Tarjetas resumen --}}
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-xs text-gray-400 uppercase">Total clientes</p>
            <p class="text-2xl font-bold mt-1" id="totalCount">{{ $customers->count() }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-xs text-gray-400 uppercase">Con deuda</p>
            <p class="text-2xl font-bold text-red-500 mt-1">{{ $withDebt }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4 col-span-2 md:col-span-1">
            <p class="text-xs text-gray-400 uppercase">Deuda total</p>
            <p class="text-2xl font-bold text-red-500 mt-1">${{ number_format($totalDebt, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Buscador en tiempo real --}}
    <div class="bg-white rounded-xl shadow p-4 mb-4">
        <div class="flex gap-3 flex-wrap">
            <div class="flex-1 relative min-w-48">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" id="searchInput"
                    placeholder="Buscar por nombre o teléfono..."
                    class="w-full border rounded-lg pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    oninput="filterCustomers()">
            </div>
            <select id="debtFilter" onchange="filterCustomers()"
                class="border rounded-lg px-3 py-2 text-sm focus:outline-none">
                <option value="">Todos</option>
                <option value="con_deuda">Con deuda</option>
                <option value="sin_deuda">Sin deuda</option>
            </select>
            <select id="sortFilter" onchange="filterCustomers()"
                class="border rounded-lg px-3 py-2 text-sm focus:outline-none">
                <option value="">Ordenar por nombre</option>
                <option value="mayor_deuda">Mayor deuda primero</option>
                <option value="menor_deuda">Menor deuda primero</option>
            </select>
        </div>
        <div class="mt-2 flex justify-between">
            <p id="resultCount" class="text-xs text-gray-400">{{ $customers->count() }} clientes</p>
            <button onclick="clearFilters()" id="clearBtn" class="text-xs text-indigo-500 hover:underline hidden">
                Limpiar filtros
            </button>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">Cliente</th>
                    <th class="px-4 py-3 text-left">Teléfono</th>
                    <th class="px-4 py-3 text-right">Deuda</th>
                    <th class="px-4 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody id="customersTable" class="divide-y divide-gray-100">
                @forelse($customers as $customer)
                    <tr class="hover:bg-gray-50 customer-row"
                        data-name="{{ strtolower($customer->name) }}"
                        data-phone="{{ $customer->phone ?? '' }}"
                        data-debt="{{ $customer->total_debt }}">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold flex-shrink-0">
                                    {{ strtoupper(substr($customer->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-medium">{{ $customer->name }}</p>
                                    @if($customer->address)
                                        <p class="text-xs text-gray-400">{{ $customer->address }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-500">
                            @if($customer->phone)
                                <a href="tel:{{ $customer->phone }}" class="hover:text-indigo-600">{{ $customer->phone }}</a>
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if($customer->total_debt > 0)
                                <span class="text-red-600 font-bold">${{ number_format($customer->total_debt, 0, ',', '.') }}</span>
                            @else
                                <span class="text-green-600 text-xs">Al día ✓</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('store.customers.show', $customer) }}"
                                   class="text-indigo-600 hover:underline text-xs px-2 py-1 rounded hover:bg-indigo-50">
                                    Ver detalle
                                </a>
                                <a href="{{ route('store.customers.edit', $customer) }}"
                                   class="text-gray-500 hover:underline text-xs px-2 py-1 rounded hover:bg-gray-50">
                                    Editar
                                </a>
                                @if($customer->phone)
                                    <a href="https://wa.me/57{{ preg_replace('/\D/', '', $customer->phone) }}"
                                       target="_blank"
                                       class="text-green-600 hover:underline text-xs px-2 py-1 rounded hover:bg-green-50">
                                        WhatsApp
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-10 text-center text-gray-400">
                            No hay clientes.
                            <a href="{{ route('store.customers.create') }}" class="text-indigo-600 hover:underline">Agrega el primero</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div id="noResults" class="hidden px-4 py-10 text-center text-gray-400">
            <p class="text-3xl mb-2">🔍</p>
            <p>No se encontraron clientes.</p>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function filterCustomers() {
    const search  = document.getElementById('searchInput').value.toLowerCase();
    const debt    = document.getElementById('debtFilter').value;
    const sort    = document.getElementById('sortFilter').value;
    const rows    = Array.from(document.querySelectorAll('.customer-row'));
    const clearBtn = document.getElementById('clearBtn');

    let visible = rows.filter(row => {
        const matchSearch = !search || row.dataset.name.includes(search) || row.dataset.phone.includes(search);
        const matchDebt   = !debt
            || (debt === 'con_deuda'  && parseFloat(row.dataset.debt) > 0)
            || (debt === 'sin_deuda'  && parseFloat(row.dataset.debt) <= 0);
        return matchSearch && matchDebt;
    });

    // Ordenar
    if (sort === 'mayor_deuda') {
        visible.sort((a, b) => parseFloat(b.dataset.debt) - parseFloat(a.dataset.debt));
    } else if (sort === 'menor_deuda') {
        visible.sort((a, b) => parseFloat(a.dataset.debt) - parseFloat(b.dataset.debt));
    }

    const tbody = document.getElementById('customersTable');
    rows.forEach(r => r.classList.add('hidden'));
    visible.forEach(r => {
        r.classList.remove('hidden');
        tbody.appendChild(r);
    });

    document.getElementById('resultCount').textContent = visible.length + ' cliente(s)';
    document.getElementById('noResults').classList.toggle('hidden', visible.length > 0);
    clearBtn.classList.toggle('hidden', !search && !debt && !sort);
}

function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('debtFilter').value  = '';
    document.getElementById('sortFilter').value  = '';
    filterCustomers();
}
</script>
@endpush