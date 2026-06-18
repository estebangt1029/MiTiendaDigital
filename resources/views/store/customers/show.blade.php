@extends('layouts.store')
@section('title', $customer->name)
@section('page-title', $customer->name)
@section('page-subtitle', 'Detalle del cliente')
@section('header-actions')
    <a href="{{ route('store.customers.edit', $customer) }}"
       class="border border-gray-200 text-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-50">
        Editar cliente
    </a>
@endsection

@section('content')
    <div class="space-y-6 max-w-4xl">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl shadow p-5 md:col-span-2">
                <h3 class="font-semibold mb-3 text-gray-700">Información</h3>
                <p class="text-sm text-gray-600">📞 {{ $customer->phone ?? 'Sin teléfono' }}</p>
                <p class="text-sm text-gray-600 mt-1">📍 {{ $customer->address ?? 'Sin dirección' }}</p>
                <p class="text-sm text-gray-600 mt-1">📅 Cliente desde {{ $customer->created_at->format('d/m/Y') }}</p>
            </div>
            <div class="bg-white rounded-xl shadow p-5 text-center">
                <p class="text-xs text-gray-400 uppercase">Deuda total</p>
                <p class="text-3xl font-bold mt-2 {{ $customer->total_debt > 0 ? 'text-red-500' : 'text-green-600' }}">
                    ${{ number_format($customer->total_debt, 0, ',', '.') }}
                </p>
                <p class="text-xs mt-1 {{ $customer->total_debt > 0 ? 'text-red-400' : 'text-green-400' }}">
                    {{ $customer->total_debt > 0 ? 'Pendiente de pago' : 'Al día ✓' }}
                </p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="px-5 py-4 border-b font-semibold text-gray-700">Historial de ventas</div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left">Fecha</th>
                        <th class="px-4 py-3 text-left">Tipo</th>
                        <th class="px-4 py-3 text-right">Total</th>
                        <th class="px-4 py-3 text-right">Pagado</th>
                        <th class="px-4 py-3 text-right">Deuda</th>
                        <th class="px-4 py-3 text-center">Estado</th>
                        <th class="px-4 py-3 text-center">Ver</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($sales as $sale)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-500">{{ $sale->created_at->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">
                                <span class="{{ $sale->type === 'fiado' ? 'text-amber-600' : 'text-gray-600' }}">
                                    {{ ucfirst($sale->type) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">${{ number_format($sale->total, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-green-600">${{ number_format($sale->paid, 0, ',', '.') }}</td>
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
                                   class="text-indigo-600 hover:underline text-xs">Ver</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-gray-400">Sin ventas registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="px-5 py-4 border-b font-semibold text-gray-700">Historial de abonos</div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left">Fecha</th>
                        <th class="px-4 py-3 text-left">Método</th>
                        <th class="px-4 py-3 text-right">Monto</th>
                        <th class="px-4 py-3 text-left">Nota</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-500">{{ $payment->created_at->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 capitalize">{{ $payment->method }}</td>
                            <td class="px-4 py-3 text-right text-green-600 font-medium">
                                ${{ number_format($payment->amount, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-gray-400">{{ $payment->notes ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-gray-400">Sin abonos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection