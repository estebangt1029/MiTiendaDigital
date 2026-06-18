@extends('layouts.store')
@section('title', 'Ventas')
@section('page-title', 'Ventas')
@section('page-subtitle', 'Historial y registro de ventas')
@section('header-actions')
    <a href="{{ route('store.sales.create') }}"
       class="bg-indigo-600 text-white px-3 py-2 lg:px-4 rounded-xl text-sm hover:bg-indigo-700 font-semibold flex items-center gap-1.5 transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        <span class="hidden sm:inline">Nueva venta</span>
        <span class="sm:hidden">Nueva</span>
    </a>
@endsection

@section('content')

    {{-- Resumen --}}
    <div class="grid grid-cols-3 gap-3 mb-5">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4">
            <p class="text-xs text-slate-400 uppercase tracking-wide">Hoy</p>
            <p class="text-xl font-bold text-indigo-600 mt-1">${{ number_format($totalHoy, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4">
            <p class="text-xs text-slate-400 uppercase tracking-wide">Pendientes</p>
            <p class="text-2xl font-bold text-red-500 mt-1">{{ $pendientes }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4">
            <p class="text-xs text-slate-400 uppercase tracking-wide">Total ventas</p>
            <p class="text-2xl font-bold text-slate-800 mt-1">{{ $sales->count() }}</p>
        </div>
    </div>

    {{-- Filtros --}}
    <form method="GET" class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 mb-4">
        <div class="flex flex-col sm:flex-row gap-2">
            <select name="status" class="flex-1 border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-slate-50">
                <option value="">Todos los estados</option>
                <option value="pagada"    {{ request('status') === 'pagada'    ? 'selected' : '' }}>Pagadas</option>
                <option value="pendiente" {{ request('status') === 'pendiente' ? 'selected' : '' }}>Pendientes</option>
                <option value="parcial"   {{ request('status') === 'parcial'   ? 'selected' : '' }}>Parciales</option>
            </select>
            <select name="type" class="flex-1 border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-slate-50">
                <option value="">Todos los tipos</option>
                <option value="contado" {{ request('type') === 'contado' ? 'selected' : '' }}>Contado</option>
                <option value="fiado"   {{ request('type') === 'fiado'   ? 'selected' : '' }}>Fiado</option>
            </select>
            <input type="date" name="date" value="{{ request('date') }}"
                class="flex-1 border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-slate-50">
            <button class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-indigo-700 transition-colors">
                Filtrar
            </button>
        </div>
    </form>

    {{-- ══ VISTA MÓVIL: cards ══ --}}
    <div class="block lg:hidden space-y-3">
        @forelse($sales as $sale)
            <a href="{{ route('store.sales.show', $sale) }}"
               class="block bg-white rounded-2xl shadow-sm border border-slate-100 p-4 hover:border-indigo-200 transition-colors">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-semibold text-slate-800">{{ $sale->customer->name ?? 'Cliente general' }}</span>
                            <span class="text-xs {{ $sale->type === 'fiado' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-600' }} px-2 py-0.5 rounded-full font-medium">
                                {{ ucfirst($sale->type) }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-400 mt-0.5">#{{ $sale->id }} · {{ $sale->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        @if($sale->status === 'pagada')
                            <span class="bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded-full text-xs font-medium">Pagada</span>
                        @elseif($sale->status === 'parcial')
                            <span class="bg-amber-100 text-amber-700 px-2.5 py-1 rounded-full text-xs font-medium">Parcial</span>
                        @else
                            <span class="bg-red-100 text-red-700 px-2.5 py-1 rounded-full text-xs font-medium">Pendiente</span>
                        @endif
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3 mt-3 pt-3 border-t border-slate-100">
                    <div>
                        <p class="text-xs text-slate-400">Total</p>
                        <p class="font-bold text-slate-800">${{ number_format($sale->total, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Deuda</p>
                        <p class="font-bold {{ $sale->debt > 0 ? 'text-red-500' : 'text-slate-300' }}">${{ number_format($sale->debt, 0, ',', '.') }}</p>
                    </div>
                </div>
            </a>
        @empty
            <div class="bg-white rounded-2xl p-12 text-center text-slate-400">
                No hay ventas.
                <a href="{{ route('store.sales.create') }}" class="text-indigo-600 hover:underline block mt-2">Registra la primera →</a>
            </div>
        @endforelse
    </div>

    {{-- ══ VISTA ESCRITORIO: tabla ══ --}}
    <div class="hidden lg:block bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 uppercase text-xs border-b border-slate-100">
                <tr>
                    <th class="px-5 py-3.5 text-left">#</th>
                    <th class="px-5 py-3.5 text-left">Fecha</th>
                    <th class="px-5 py-3.5 text-left">Cliente</th>
                    <th class="px-5 py-3.5 text-left">Tipo</th>
                    <th class="px-5 py-3.5 text-right">Total</th>
                    <th class="px-5 py-3.5 text-right">Deuda</th>
                    <th class="px-5 py-3.5 text-center">Estado</th>
                    <th class="px-5 py-3.5 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($sales as $sale)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="px-5 py-3.5 text-slate-400 font-mono text-xs">#{{ $sale->id }}</td>
                        <td class="px-5 py-3.5 text-slate-500">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-5 py-3.5 font-medium text-slate-800">{{ $sale->customer->name ?? 'Cliente general' }}</td>
                        <td class="px-5 py-3.5">
                            <span class="text-xs px-2.5 py-1 rounded-full font-medium {{ $sale->type === 'fiado' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ ucfirst($sale->type) }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-right font-semibold text-slate-800">${{ number_format($sale->total, 0, ',', '.') }}</td>
                        <td class="px-5 py-3.5 text-right {{ $sale->debt > 0 ? 'text-red-500 font-semibold' : 'text-slate-300' }}">
                            ${{ number_format($sale->debt, 0, ',', '.') }}
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            @if($sale->status === 'pagada')
                                <span class="bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded-full text-xs font-medium">Pagada</span>
                            @elseif($sale->status === 'parcial')
                                <span class="bg-amber-100 text-amber-700 px-2.5 py-1 rounded-full text-xs font-medium">Parcial</span>
                            @else
                                <span class="bg-red-100 text-red-700 px-2.5 py-1 rounded-full text-xs font-medium">Pendiente</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <a href="{{ route('store.sales.show', $sale) }}"
                               class="text-indigo-600 hover:bg-indigo-50 text-xs px-3 py-1.5 rounded-lg transition-colors font-medium">
                                Ver detalle
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-5 py-12 text-center text-slate-400">
                            No hay ventas. <a href="{{ route('store.sales.create') }}" class="text-indigo-600 hover:underline">Registra la primera</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection
