@extends('layouts.store')
@section('title', 'Editar Proveedor')
@section('page-title', 'Editar proveedor')
@section('page-subtitle', $supplier->name)

@section('content')
    <div class="max-w-xl mx-auto">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <form method="POST" action="{{ route('store.suppliers.update', $supplier) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nombre del proveedor *</label>
                    <input type="text" name="name" value="{{ old('name', $supplier->name) }}" required
                        class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-slate-50">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Persona de contacto</label>
                    <input type="text" name="contact_name" value="{{ old('contact_name', $supplier->contact_name) }}"
                        class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-slate-50">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Teléfono</label>
                        <input type="text" name="phone" value="{{ old('phone', $supplier->phone) }}"
                            class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-slate-50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                        <input type="email" name="email" value="{{ old('email', $supplier->email) }}"
                            class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-slate-50">
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Dirección</label>
                    <input type="text" name="address" value="{{ old('address', $supplier->address) }}"
                        class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-slate-50">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Notas</label>
                    <textarea name="notes" rows="3"
                        class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-slate-50">{{ old('notes', $supplier->notes) }}</textarea>
                </div>

                @if($supplier->total_debt > 0)
                    <div class="bg-amber-50 border border-amber-200 text-amber-700 px-4 py-3 rounded-xl text-sm">
                        ⚠ Este proveedor tiene una deuda pendiente de <strong>${{ number_format($supplier->total_debt, 0, ',', '.') }}</strong>.
                    </div>
                @endif

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                        class="flex-1 bg-indigo-600 text-white py-3 rounded-xl font-semibold hover:bg-indigo-700 transition-colors shadow-sm">
                        Guardar cambios
                    </button>
                    <a href="{{ route('store.suppliers.show', $supplier) }}"
                       class="flex-1 text-center border border-slate-200 py-3 rounded-xl text-slate-600 hover:bg-slate-50 transition-colors">
                        Cancelar
                    </a>
                </div>
            </form>

            @if($supplier->total_debt <= 0)
                <form method="POST" action="{{ route('store.suppliers.destroy', $supplier) }}"
                      class="mt-4 pt-4 border-t border-slate-100"
                      onsubmit="return confirm('¿Desactivar este proveedor? Podrás reactivarlo más adelante si lo necesitas.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-500 hover:underline text-sm">
                        Desactivar proveedor
                    </button>
                </form>
            @endif
        </div>
    </div>
@endsection