@extends('layouts.store')
@section('title', 'Nuevo Proveedor')
@section('page-title', 'Nuevo proveedor')
@section('page-subtitle', 'Registra un nuevo proveedor para tu tienda')

@section('content')
    <div class="max-w-xl mx-auto">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <form method="POST" action="{{ route('store.suppliers.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nombre del proveedor *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        placeholder="Ej: Distribuidora La Económica"
                        class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-slate-50">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Persona de contacto</label>
                    <input type="text" name="contact_name" value="{{ old('contact_name') }}"
                        placeholder="Ej: Juan Pérez"
                        class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-slate-50">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Teléfono</label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                            placeholder="300 123 4567"
                            class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-slate-50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                            placeholder="proveedor@correo.com"
                            class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-slate-50">
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Dirección</label>
                    <input type="text" name="address" value="{{ old('address') }}"
                        placeholder="Dirección de la bodega o punto de despacho"
                        class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-slate-50">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Notas</label>
                    <textarea name="notes" rows="3"
                        placeholder="Ej: Entrega los lunes y jueves. Pedido mínimo $200.000"
                        class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-slate-50">{{ old('notes') }}</textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                        class="flex-1 bg-indigo-600 text-white py-3 rounded-xl font-semibold hover:bg-indigo-700 transition-colors shadow-sm">
                        Guardar proveedor
                    </button>
                    <a href="{{ route('store.suppliers.index') }}"
                       class="flex-1 text-center border border-slate-200 py-3 rounded-xl text-slate-600 hover:bg-slate-50 transition-colors">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection