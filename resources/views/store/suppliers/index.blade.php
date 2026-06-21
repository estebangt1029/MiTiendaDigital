@extends('layouts.store')
@section('title', 'Proveedores')
@section('page-title', 'Proveedores')
@section('page-subtitle', 'Gestión de proveedores y cuentas por pagar')
@section('header-actions')
    <a href="{{ route('store.suppliers.create') }}"
       class="bg-indigo-600 text-white px-3 py-2 lg:px-4 rounded-xl text-sm hover:bg-indigo-700 font-semibold flex items-center gap-1.5 transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        <span class="hidden sm:inline">Nuevo proveedor</span>
        <span class="sm:hidden">Nuevo</span>
    </a>
@endsection

@section('content')

    {{-- Tarjetas resumen --}}
    <div class="grid grid-cols-3 gap-3 mb-5">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4">
            <p class="text-xs text-slate-400 uppercase tracking-wide">Proveedores</p>
            <p class="text-2xl font-bold text-slate-800 mt-1">{{ $suppliers->count() }}</p>
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
        <form method="GET" class="flex flex-col sm:flex-row gap-2">
            <div class="flex-1 relative">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Buscar por nombre, contacto o teléfono..."
                    class="w-full border border-slate-200 rounded-xl pl-9 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-slate-50">
            </div>
            <select name="filter" onchange="this.form.submit()"
                class="border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-slate-50">
                <option value="">Todos</option>
                <option value="con_deuda" {{ request('filter') === 'con_deuda' ? 'selected' : '' }}>Con deuda</option>
                <option value="sin_deuda" {{ request('filter') === 'sin_deuda' ? 'selected' : '' }}>Sin deuda</option>
            </select>
            <button class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-indigo-700 transition-colors">
                Buscar
            </button>
        </form>
    </div>

    {{-- ══ VISTA MÓVIL: cards ══ --}}
    <div class="block lg:hidden space-y-3">
        @forelse($suppliers as $supplier)
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-base font-bold flex-shrink-0">
                        {{ strtoupper(substr($supplier->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-slate-800 truncate">{{ $supplier->name }}</p>
                        <p class="text-xs text-slate-400">
                            {{ $supplier->contact_name ?? 'Sin contacto' }}
                            @if($supplier->phone) · {{ $supplier->phone }}@endif
                        </p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        @if($supplier->total_debt > 0)
                            <p class="font-bold text-red-500">${{ number_format($supplier->total_debt, 0, ',', '.') }}</p>
                            <p class="text-xs text-red-400">le debes</p>
                        @else
                            <p class="text-emerald-600 text-xs font-medium">Al día ✓</p>
                        @endif
                    </div>
                </div>

                <div class="flex gap-2 mt-3 pt-3 border-t border-slate-100">
                    <a href="{{ route('store.suppliers.show', $supplier) }}"
                       class="flex-1 text-center text-indigo-600 text-sm py-2 rounded-xl bg-indigo-50 hover:bg-indigo-100 font-medium transition-colors">
                        Ver detalle
                    </a>
                    <a href="{{ route('store.suppliers.edit', $supplier) }}"
                       class="flex-1 text-center text-slate-500 text-sm py-2 rounded-xl bg-slate-50 hover:bg-slate-100 font-medium transition-colors">
                        Editar
                    </a>
                    @if($supplier->phone)
                        <a href="https://wa.me/57{{ preg_replace('/\D/', '', $supplier->phone) }}"
                           target="_blank"
                           class="flex-1 text-center text-emerald-600 text-sm py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 font-medium transition-colors">
                            WhatsApp
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl p-12 text-center text-slate-400">
                No hay proveedores registrados.
                <a href="{{ route('store.suppliers.create') }}" class="text-indigo-600 hover:underline block mt-2">Agrega el primero →</a>
            </div>
        @endforelse
    </div>

    {{-- ══ VISTA ESCRITORIO: tabla ══ --}}
    <div class="hidden lg:block bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 uppercase text-xs border-b border-slate-100">
                <tr>
                    <th class="px-5 py-3.5 text-left">Proveedor</th>
                    <th class="px-5 py-3.5 text-left">Contacto</th>
                    <th class="px-5 py-3.5 text-left">Teléfono</th>
                    <th class="px-5 py-3.5 text-right">Deuda</th>
                    <th class="px-5 py-3.5 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($suppliers as $supplier)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-sm font-bold flex-shrink-0">
                                    {{ strtoupper(substr($supplier->name, 0, 1)) }}
                                </div>
                                <p class="font-medium text-slate-800">{{ $supplier->name }}</p>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-slate-500">{{ $supplier->contact_name ?? '—' }}</td>
                        <td class="px-5 py-3.5 text-slate-500">
                            @if($supplier->phone)
                                <a href="tel:{{ $supplier->phone }}" class="hover:text-indigo-600 transition-colors">{{ $supplier->phone }}</a>
                            @else
                                <span class="text-slate-300">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            @if($supplier->total_debt > 0)
                                <span class="text-red-500 font-bold">${{ number_format($supplier->total_debt, 0, ',', '.') }}</span>
                            @else
                                <span class="text-emerald-600 text-xs font-medium">Al día ✓</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <div class="flex justify-center gap-1.5">
                                <a href="{{ route('store.suppliers.show', $supplier) }}"
                                   class="text-indigo-600 hover:bg-indigo-50 text-xs px-3 py-1.5 rounded-lg transition-colors font-medium">
                                    Ver detalle
                                </a>
                                <a href="{{ route('store.suppliers.edit', $supplier) }}"
                                   class="text-slate-500 hover:bg-slate-100 text-xs px-3 py-1.5 rounded-lg transition-colors font-medium">
                                    Editar
                                </a>
                                @if($supplier->phone)
                                    <a href="https://wa.me/57{{ preg_replace('/\D/', '', $supplier->phone) }}"
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
                        <td colspan="5" class="px-5 py-12 text-center text-slate-400">
                            No hay proveedores registrados.
                            <a href="{{ route('store.suppliers.create') }}" class="text-indigo-600 hover:underline">Agrega el primero</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection