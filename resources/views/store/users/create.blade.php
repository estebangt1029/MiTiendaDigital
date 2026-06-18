@extends('layouts.store')
@section('title', 'Nuevo empleado')
@section('page-title', 'Nuevo empleado')

@section('content')
    <div class="max-w-lg">
        <div class="bg-white rounded-xl shadow p-8">
            <form method="POST" action="{{ route('store.users.store') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Nombre *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Email *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Rol *</label>
                    <select name="role" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="cajero">Cajero — solo ventas y clientes</option>
                        <option value="inventario">Inventario — solo productos y stock</option>
                        <option value="admin">Admin — acceso completo</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Contraseña *</label>
                    <input type="password" name="password" required
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium mb-1">Confirmar contraseña *</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <button type="submit"
                    class="w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 font-medium">
                    Crear empleado
                </button>
            </form>
        </div>
    </div>
@endsection