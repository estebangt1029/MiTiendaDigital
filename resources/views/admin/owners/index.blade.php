@extends('layouts.admin')
@section('title', 'Dueños')
@section('page-title', 'Dueños registrados')
@section('header-actions')
    <a href="{{ route('admin.owners.create') }}"
       class="bg-gray-900 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-700">
        + Nuevo dueño
    </a>
@endsection

@section('content')
    <form method="GET" class="flex gap-3 mb-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre o email..."
            class="flex-1 border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-800">
        <button class="bg-gray-700 text-white px-4 py-2 rounded-lg text-sm">Buscar</button>
    </form>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">Dueño</th>
                    <th class="px-4 py-3 text-left">Teléfono</th>
                    <th class="px-4 py-3 text-center">Tiendas</th>
                    <th class="px-4 py-3 text-center">Estado</th>
                    <th class="px-4 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($owners as $owner)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <p class="font-medium">{{ $owner->name }}</p>
                            <p class="text-xs text-gray-400">{{ $owner->email }}</p>
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $owner->phone ?? '—' }}</td>
                        <td class="px-4 py-3 text-center">{{ $owner->stores_count }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs {{ $owner->active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $owner->active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center flex justify-center gap-3">
                            <a href="{{ route('admin.owners.show', $owner) }}"
                               class="text-indigo-600 hover:underline text-xs">Ver</a>
                            <form method="POST" action="{{ route('admin.owners.toggle', $owner) }}">
                                @csrf
                                <button class="text-xs {{ $owner->active ? 'text-red-400 hover:text-red-600' : 'text-green-500 hover:text-green-700' }}">
                                    {{ $owner->active ? 'Desactivar' : 'Activar' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-400">No hay dueños registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection