@extends('layouts.store')
@section('title', 'Ventas')
@section('page-title', 'Ventas')
@section('page-subtitle', 'Historial y registro de ventas')
@section('header-actions')
    <a href="{{ route('store.sales.create') }}"
       class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 font-medium">
        + Nueva venta
    </a>
@endsection

@section('content')
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-xs text-gray-400 uppercase">Ventas hoy</p>
            <p class="text-2xl font-bold text-indigo-600 mt-1">${{ number_format($totalHoy, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-xs text-gray-400 uppercase">Pendientes de pago</p>
            <p class="text-2xl font-bold text-red-500 mt-1">{{ $pendientes }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4 col-span-2 md:col-span-1">
            <p class="text-xs text-gray-400 uppercase">Total ventas</p>
            <p class="text-2xl font-bold mt-1">{{ $sales->count() }}</p>
        </div>
    </div>

    <form method="GET" class="flex gap-3 mb-4 flex-wrap">
        <select name="status" class="border rounded-lg px-3 py-2 text-sm">
            <option value="">Todos los estados</option>
            <option value="pagada"    {{ request('status') === 'pagada'    ? 'selected' : '' }}>Pagadas</option>
            <option value="pendiente" {{ request('status') === 'pendiente' ? 'selected' : '' }}>Pendientes</option>
            <option value="parcial"   {{ request('status') === 'parcial'   ? 'selected' : '' }}>Parciales</option>
        </select>
        <select name="type" class="border rounded-lg px-3 py-2 text-sm">
            <option value="">Todos los tipos</option>
            <option value="contado" {{ request('type') === 'contado' ? 'selected' : '' }}>Contado</option>
            <option value="fiado"   {{ request('type') === 'fiado'   ? 'selected' : '' }}>Fiado</option>
        </select>
        <input type="date" name="date" value="{{ request('date') }}"
            class="border rounded-lg px-3 py-2 text-sm">
        <button class="bg-gray-700 text-white px-4 py-2 rounded-lg text-sm">Filtrar</button>
    </form>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">#</th>
                    <th class="px-4 py-3 text-left">Fecha</th>
                    <th class="px-4 py-3 text-left">Cliente</th>
                    <th class="px-4 py-3 text-left">Tipo</th>
                    <th class="px-4 py-3 text-right">Total</th>
                    <th class="px-4 py-3 text-right">Deuda</th>
                    <th class="px-4 py-3 text-center">Estado</th>
                    <th class="px-4 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($sales as $sale)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-400">#{{ $sale->id }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3">{{ $sale->customer->name ?? 'Cliente general' }}</td>
                        <td class="px-4 py-3">
                            <span class="{{ $sale->type === 'fiado' ? 'text-amber-600' : 'text-gray-600' }}">
                                {{ ucfirst($sale->type) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right font-medium">${{ number_format($sale->total, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right {{ $sale->debt > 0 ? 'text-red-600 font-medium' : 'text-gray-400' }}">
                            ${{ number_format($sale->debt, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($sale->status === 'pagada')
                                <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-xs">Pagada</span>
                            @elseif($sale->status === 'parcial')
                                <span class="bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full text-xs">Parcial</span>
                            @else
                                <span class="bg-red-100 text-red-700 px-2 py-0.5 rounded-full text-xs">Pendiente</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('store.sales.show', $sale) }}"
                               class="text-indigo-600 hover:underline text-xs">Ver detalle</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-400">
                            No hay ventas. <a href="{{ route('store.sales.create') }}" class="text-indigo-600 hover:underline">Registra la primera</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection