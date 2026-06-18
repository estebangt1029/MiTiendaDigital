@extends('layouts.store')
@section('title', 'Nuevo cliente') {{-- o 'Editar cliente' --}}
@section('page-title', 'Nuevo cliente') {{-- o 'Editar cliente' --}}

@section('content')
    <div class="max-w-lg">
        <div class="bg-white rounded-xl shadow p-8">
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">{{ $errors->first() }}</div>
            @endif

            {{-- El form igual que antes, solo sin el HTML externo --}}
            <form method="POST" action="{{ route('store.customers.store') }}"> {{-- o update --}}
                @csrf {{-- agregar @method('PUT') en edit --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Nombre *</label>
                    <input type="text" name="name" value="{{ old('name', $customer->name ?? '') }}" required
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Teléfono</label>
                    <input type="text" name="phone" value="{{ old('phone', $customer->phone ?? '') }}"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium mb-1">Dirección</label>
                    <input type="text" name="address" value="{{ old('address', $customer->address ?? '') }}"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <button type="submit"
                    class="w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 font-medium">
                    Guardar
                </button>
            </form>
        </div>
    </div>
@endsection