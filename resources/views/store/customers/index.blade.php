@extends('layouts.store')
@section('title', 'Clientes')
@section('page-title', 'Clientes')
@section('page-subtitle', 'Gestión de clientes y fiados')
@section('header-actions')
    <a href="{{ route('store.customers.create') }}"
       class="bg-indigo-600 text-white px-3 py-2 lg:px-4 rounded-xl text-sm hover:bg-indigo-700 font-semibold flex items-center gap-1.5 transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        <span class="hidden sm:inline">Nuevo cliente</span>
        <span class="sm:hidden">Nuevo</span>
    </a>
@endsection

@section('content')

    {{-- Tarjetas resumen --}}
    <div class="grid grid-cols-3 gap-3 mb-5">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4">
            <p class="text-xs text-slate-400 uppercase tracking-wide">Clientes</p>
            <p class="text-2xl font-bold text-slate-800 mt-1">{{ $customers->count() }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4">
            <p class="text-xs text-slate-400 uppercase tracking-wide">Con deuda</p>
            <p class="text-2xl font-bold text-red-500 mt-1">{{ $withDebt }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4">
            <p class="text-xs text-slate-400 uppercase tracking-wide">Deuda total</p>
            <p class="text-xl font-bold text-red-500 mt-1">${{ number_format($totalDebt, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Buscador --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 mb-4">
        <div class="flex flex-col sm:flex-row gap-2">
            <div class="flex-1 relative">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" id="searchInput"
                    placeholder="Buscar por nombre o teléfono..."
                    class="w-full border border-slate-200 rounded-xl pl-9 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-slate-50"
                    oninput="filterCustomers()">
            </div>
            <div class="flex gap-2">
                <select id="debtFilter" onchange="filterCustomers()"
                    class="flex-1 border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-slate-50">
                    <option value="">Todos</option>
                    <option value="con_deuda">Con deuda</option>
                    <option value="sin_deuda">Sin deuda</option>
                </select>
                <select id="sortFilter" onchange="filterCustomers()"
                    class="flex-1 border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-slate-50">
                    <option value="">Ordenar</option>
                    <option value="mayor_deuda">Mayor deuda</option>
                    <option value="menor_deuda">Menor deuda</option>
                </select>
            </div>
        </div>
        <div class="mt-2 flex justify-between">
            <p id="resultCount" class="text-xs text-slate-400">{{ $customers->count() }} clientes</p>
            <button onclick="clearFilters()" id="clearBtn" class="text-xs text-indigo-500 hover:underline hidden">
                Limpiar filtros
            </button>
        </div>
    </div>

    {{-- ══ VISTA MÓVIL: cards ══ --}}
    <div class="block lg:hidden space-y-3" id="customerCardsMobile">
        @forelse($customers as $customer)
            <div class="customer-row bg-white rounded-2xl shadow-sm border border-slate-100 p-4"
                 data-name="{{ strtolower($customer->name) }}"
                 data-phone="{{ $customer->phone ?? '' }}"
                 data-debt="{{ $customer->total_debt }}">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-base font-bold flex-shrink-0">
                        {{ strtoupper(substr($customer->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-slate-800 truncate">{{ $customer->name }}</p>
                        <p class="text-xs text-slate-400">
                            {{ $customer->phone ?? 'Sin teléfono' }}
                            @if($customer->address) · {{ $customer->address }}@endif
                        </p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        @if($customer->total_debt > 0)
                            <p class="font-bold text-red-500">${{ number_format($customer->total_debt, 0, ',', '.') }}</p>
                            <p class="text-xs text-red-400">debe</p>
                        @else
                            <p class="text-emerald-600 text-xs font-medium">Al día ✓</p>
                        @endif
                    </div>
                </div>

                <div class="flex gap-2 mt-3 pt-3 border-t border-slate-100">
                    <a href="{{ route('store.customers.show', $customer) }}"
                       class="flex-1 text-center text-indigo-600 text-sm py-2 rounded-xl bg-indigo-50 hover:bg-indigo-100 font-medium transition-colors">
                        Ver detalle
                    </a>
                    <a href="{{ route('store.customers.edit', $customer) }}"
                       class="flex-1 text-center text-slate-500 text-sm py-2 rounded-xl bg-slate-50 hover:bg-slate-100 font-medium transition-colors">
                        Editar
                    </a>
                    @if($customer->phone)
                        <a href="https://wa.me/57{{ preg_replace('/\D/', '', $customer->phone) }}"
                           target="_blank"
                           class="flex-1 text-center text-emerald-600 text-sm py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 font-medium transition-colors">
                            WhatsApp
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl p-12 text-center text-slate-400">
                No hay clientes.
                <a href="{{ route('store.customers.create') }}" class="text-indigo-600 hover:underline block mt-2">Agrega el primero →</a>
            </div>
        @endforelse
    </div>

    {{-- ══ VISTA ESCRITORIO: tabla ══ --}}
    <div class="hidden lg:block bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 uppercase text-xs border-b border-slate-100">
                <tr>
                    <th class="px-5 py-3.5 text-left">Cliente</th>
                    <th class="px-5 py-3.5 text-left">Teléfono</th>
                    <th class="px-5 py-3.5 text-right">Deuda</th>
                    <th class="px-5 py-3.5 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody id="customersTable" class="divide-y divide-slate-100">
                @forelse($customers as $customer)
                    <tr class="hover:bg-slate-50/70 customer-row transition-colors"
                        data-name="{{ strtolower($customer->name) }}"
                        data-phone="{{ $customer->phone ?? '' }}"
                        data-debt="{{ $customer->total_debt }}">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-sm font-bold flex-shrink-0">
                                    {{ strtoupper(substr($customer->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-medium text-slate-800">{{ $customer->name }}</p>
                                    @if($customer->address)
                                        <p class="text-xs text-slate-400">{{ $customer->address }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-slate-500">
                            @if($customer->phone)
                                <a href="tel:{{ $customer->phone }}" class="hover:text-indigo-600 transition-colors">{{ $customer->phone }}</a>
                            @else
                                <span class="text-slate-300">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            @if($customer->total_debt > 0)
                                <span class="text-red-500 font-bold">${{ number_format($customer->total_debt, 0, ',', '.') }}</span>
                            @else
                                <span class="text-emerald-600 text-xs font-medium">Al día ✓</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <div class="flex justify-center gap-1.5">
                                <a href="{{ route('store.customers.show', $customer) }}"
                                   class="text-indigo-600 hover:bg-indigo-50 text-xs px-3 py-1.5 rounded-lg transition-colors font-medium">
                                    Ver detalle
                                </a>
                                <a href="{{ route('store.customers.edit', $customer) }}"
                                   class="text-slate-500 hover:bg-slate-100 text-xs px-3 py-1.5 rounded-lg transition-colors font-medium">
                                    Editar
                                </a>
                                @if($customer->phone)
                                    <a href="https://wa.me/57{{ preg_replace('/\D/', '', $customer->phone) }}"
                                       target="_blank"
                                       class="text-emerald-600 hover:bg-emerald-50 text-xs px-3 py-1.5 rounded-lg transition-colors font-medium">
                                        WhatsApp
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-12 text-center text-slate-400">
                            No hay clientes.
                            <a href="{{ route('store.customers.create') }}" class="text-indigo-600 hover:underline">Agrega el primero</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div id="noResults" class="hidden px-4 py-12 text-center text-slate-400">
            <p class="text-4xl mb-3">🔍</p>
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
            || (debt === 'con_deuda' && parseFloat(row.dataset.debt) > 0)
            || (debt === 'sin_deuda' && parseFloat(row.dataset.debt) <= 0);
        return matchSearch && matchDebt;
    });

    if (sort === 'mayor_deuda') visible.sort((a, b) => parseFloat(b.dataset.debt) - parseFloat(a.dataset.debt));
    else if (sort === 'menor_deuda') visible.sort((a, b) => parseFloat(a.dataset.debt) - parseFloat(b.dataset.debt));

    const containers = document.querySelectorAll('#customersTable, #customerCardsMobile');
    rows.forEach(r => r.classList.add('hidden'));
    visible.forEach(r => {
        r.classList.remove('hidden');
        // Re-ordenar en su contenedor padre
        r.parentNode.appendChild(r);
    });

    document.getElementById('resultCount').textContent = visible.length + ' cliente(s)';
    document.getElementById('noResults')?.classList.toggle('hidden', visible.length > 0);
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
