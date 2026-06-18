@extends('layouts.storeuser')
@section('title', 'Ventas')
@section('page-title', 'Ventas')
@section('page-subtitle', 'Registro y historial de ventas')
@section('header-actions')
    <a href="{{ route('employee.sales.create') }}"
       class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 font-medium">
        + Nueva venta
    </a>
@endsection

@section('content')
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-xs text-gray-400 uppercase">Ventas hoy</p>
            <p class="text-2xl font-bold text-green-600 mt-1">${{ number_format($totalHoy, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-xs text-gray-400 uppercase">Pendientes</p>
            <p class="text-2xl font-bold text-red-500 mt-1">{{ $pendientes }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-xs text-gray-400 uppercase">Total registros</p>
            <p class="text-2xl font-bold mt-1">{{ $sales->count() }}</p>
        </div>
    </div>

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
                    <th class="px-4 py-3 text-center">Ver</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($sales as $sale)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-400">#{{ $sale->id }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3">{{ $sale->customer->name ?? 'General' }}</td>
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
                            <a href="{{ route('employee.sales.show', $sale) }}"
                               class="text-green-600 hover:underline text-xs">Ver</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-400">
                            No hay ventas aún.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection