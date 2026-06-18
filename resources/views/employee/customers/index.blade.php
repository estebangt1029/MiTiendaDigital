@extends('layouts.storeuser')
@section('title', 'Clientes')
@section('page-title', 'Clientes')

@section('content')
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-xs text-gray-400 uppercase">Total clientes</p>
            <p class="text-2xl font-bold mt-1">{{ $customers->count() }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-xs text-gray-400 uppercase">Con deuda</p>
            <p class="text-2xl font-bold text-red-500 mt-1">{{ $withDebt }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-xs text-gray-400 uppercase">Deuda total</p>
            <p class="text-2xl font-bold text-red-500 mt-1">${{ number_format($totalDebt, 0, ',', '.') }}</p>
        </div>
    </div>

    <form method="GET" class="flex gap-3 mb-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar cliente..."
            class="flex-1 border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
        <button class="bg-gray-700 text-white px-4 py-2 rounded-lg text-sm">Buscar</button>
    </form>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">Cliente</th>
                    <th class="px-4 py-3 text-left">Teléfono</th>
                    <th class="px-4 py-3 text-right">Deuda</th>
                    <th class="px-4 py-3 text-center">Ver</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($customers as $customer)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">{{ $customer->name }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $customer->phone ?? '—' }}</td>
                        <td class="px-4 py-3 text-right {{ $customer->total_debt > 0 ? 'text-red-600 font-bold' : 'text-green-600' }}">
                            {{ $customer->total_debt > 0 ? '$'.number_format($customer->total_debt, 0, ',', '.') : 'Al día ✓' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('employee.customers.show', $customer) }}"
                               class="text-green-600 hover:underline text-xs">Ver detalle</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">No hay clientes.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection