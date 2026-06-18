@extends('layouts.owner')
@section('title', 'Nueva Tienda')

@section('content')
<div class="max-w-lg mx-auto mt-8 px-4">
    <div class="bg-white rounded-xl shadow p-8">
        <h2 class="text-xl font-bold mb-6">Nueva tienda</h2>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 p-3 rounded-lg mb-4 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('owner.stores.store') }}">
            @csrf

            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Datos de la tienda</p>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Nombre *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Dirección</label>
                <input type="text" name="address" value="{{ old('address') }}"
                    class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium mb-1">Teléfono</label>
                <input type="text" name="phone" value="{{ old('phone') }}"
                    class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="border-t pt-6 mb-6">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Elige tu plan</p>
                <div class="space-y-3">
                    @foreach($plans as $key => $plan)
                        <label class="flex items-center justify-between border-2 rounded-xl p-4 cursor-pointer transition-all"
                               id="label_{{ $key }}">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="plan" value="{{ $key }}"
                                       {{ old('plan', '1_month') === $key ? 'checked' : '' }}
                                       class="text-indigo-600"
                                       onchange="selectPlan('{{ $key }}')">
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $plan['label'] }}</p>
                                    <p class="text-xs text-gray-400">
                                        @if($key === '3_months') Ahorra 7%
                                        @elseif($key === '6_months') Ahorra 10%
                                        @elseif($key === '1_year') ⭐ Ahorra 17%
                                        @else Precio estándar
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-indigo-600">${{ number_format($plan['price'], 0, ',', '.') }}</p>
                                <p class="text-xs text-gray-400">COP</p>
                            </div>
                        </label>
                    @endforeach
                </div>

                <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mt-4 text-sm text-blue-700">
                    <p class="font-semibold mb-1">💳 ¿Cómo pagar?</p>
                    <p>Después de crear la tienda te contactamos por WhatsApp con los datos de pago. La activamos en minutos.</p>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                    class="flex-1 bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 font-medium">
                    Crear tienda
                </button>
                <a href="{{ route('owner.dashboard') }}"
                    class="flex-1 text-center border py-2 rounded-lg hover:bg-gray-50 text-gray-600">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const checked = document.querySelector('input[name="plan"]:checked');
    if (checked) selectPlan(checked.value);
});

function selectPlan(key) {
    document.querySelectorAll('[id^="label_"]').forEach(el => {
        el.classList.remove('border-indigo-500', 'bg-indigo-50');
        el.classList.add('border-gray-200');
    });
    const selected = document.getElementById('label_' + key);
    if (selected) {
        selected.classList.remove('border-gray-200');
        selected.classList.add('border-indigo-500', 'bg-indigo-50');
    }
}
</script>
@endsection