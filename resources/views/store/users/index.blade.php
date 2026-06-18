@extends('layouts.store')
@section('title', 'Empleados')
@section('page-title', 'Empleados')
@section('page-subtitle', 'Gestión de empleados de la tienda')
@section('header-actions')
    <a href="{{ route('store.users.create') }}"
       class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 font-medium">
        + Nuevo empleado
    </a>
@endsection

@section('content')
    <div class="bg-white rounded-xl shadow overflow-hidden max-w-4xl">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">Nombre</th>
                    <th class="px-4 py-3 text-left">Email</th>
                    <th class="px-4 py-3 text-center">Rol</th>
                    <th class="px-4 py-3 text-center">Estado</th>
                    <th class="px-4 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">{{ $user->name }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $user->email }}</td>
                        <td class="px-4 py-3 text-center">
                            @php $colors = ['admin' => 'indigo', 'cajero' => 'green', 'inventario' => 'amber']; $c = $colors[$user->role] ?? 'gray'; @endphp
                            <span class="bg-{{ $c }}-100 text-{{ $c }}-700 px-2 py-0.5 rounded-full text-xs capitalize">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="{{ $user->active ? 'text-green-600' : 'text-red-400' }} text-xs">
                                {{ $user->active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center flex justify-center gap-3">
                            <a href="{{ route('store.users.edit', $user) }}"
                               class="text-indigo-600 hover:underline text-xs">Editar</a>
                            <form method="POST" action="{{ route('store.users.destroy', $user) }}"
                                  onsubmit="return confirm('¿Desactivar empleado?')">
                                @csrf @method('DELETE')
                                <button class="text-red-400 hover:text-red-600 text-xs">Desactivar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-400">
                            No hay empleados. <a href="{{ route('store.users.create') }}" class="text-indigo-600 hover:underline">Agrega el primero</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection