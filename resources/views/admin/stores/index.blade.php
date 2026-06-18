@extends('layouts.admin')
@section('title', 'Tiendas')
@section('page-title', 'Tiendas')
@section('page-subtitle', 'Todas las tiendas del sistema')

@section('content')
    <form method="GET" class="flex gap-3 mb-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar tienda..."
            class="flex-1 border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-800">
        <select name="status" class="border rounded-lg px-3 py-2 text-sm">
            <option value="">Todas</option>
            <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Activas</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactivas</option>
        </select>
        <button class="bg-gray-700 text-white px-4 py-2 rounded-lg text-sm">Filtrar</button>
    </form>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">Tienda</th>
                    <th class="px-4 py-3 text-left">Dueño</th>
                    <th class="px-4 py-3 text-center">Productos</th>
                    <th class="px-4 py-3 text-center">Clientes</th>
                    <th class="px-4 py-3 text-center">Ventas</th>
                    <th class="px-4 py-3 text-center">Estado</th>
                    <th class="px-4 py-3 text-center">Acción</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($stores as $store)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <p class="font-medium">{{ $store->name }}</p>
                            <p class="text-xs text-gray-400">{{ $store->address ?? '—' }}</p>
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $store->owner->name }}</td>
                        <td class="px-4 py-3 text-center">{{ $store->products_count }}</td>
                        <td class="px-4 py-3 text-center">{{ $store->customers_count }}</td>
                        <td class="px-4 py-3 text-center">{{ $store->sales_count }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs {{ $store->active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $store->active ? 'Activa' : 'Inactiva' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <form method="POST" action="{{ route('admin.stores.toggle', $store) }}">
                                @csrf
                                <button class="text-xs {{ $store->active ? 'text-red-400 hover:text-red-600' : 'text-green-500 hover:text-green-700' }}">
                                    {{ $store->active ? 'Desactivar' : 'Activar' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-400">No hay tiendas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection