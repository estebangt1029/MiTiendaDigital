@extends('layouts.store')
@section('title', 'Editar empleado')
@section('page-title', 'Editar empleado')

@section('content')
    <div class="max-w-lg">
        <div class="bg-white rounded-xl shadow p-8">
            <form method="POST" action="{{ route('store.users.update', $storeUser) }}">
                @csrf @method('PUT')
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Nombre *</label>
                    <input type="text" name="name" value="{{ old('name', $storeUser->name) }}" required
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Email *</label>
                    <input type="email" name="email" value="{{ old('email', $storeUser->email) }}" required
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Rol *</label>
                    <select name="role" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="cajero"     {{ $storeUser->role === 'cajero'     ? 'selected' : '' }}>Cajero</option>
                        <option value="inventario" {{ $storeUser->role === 'inventario' ? 'selected' : '' }}>Inventario</option>
                        <option value="admin"      {{ $storeUser->role === 'admin'      ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Nueva contraseña <span class="text-gray-400">(dejar vacío para no cambiar)</span></label>
                    <input type="password" name="password"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium mb-1">Confirmar contraseña</label>
                    <input type="password" name="password_confirmation"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <button type="submit"
                    class="w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 font-medium">
                    Guardar cambios
                </button>
            </form>
        </div>
    </div>
@endsection