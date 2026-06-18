@extends('layouts.admin')
@section('title', 'Pendientes')
@section('page-title', 'Suscripciones pendientes')
@section('page-subtitle', 'Registros esperando confirmación de pago')

@section('content')
    @if($subscriptions->isEmpty())
        <div class="bg-white rounded-xl shadow p-10 text-center text-gray-400">
            <p class="text-lg">✓ No hay suscripciones pendientes</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($subscriptions as $sub)
                @php $plan = \App\Models\Subscription::plans()[$sub->plan]; @endphp
                <div class="bg-white rounded-xl shadow p-6">
                    <div class="flex justify-between items-start flex-wrap gap-4">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full text-xs font-medium">
                                    Pendiente
                                </span>
                                <span class="text-gray-400 text-xs">{{ $sub->created_at->diffForHumans() }}</span>
                            </div>
                            <h3 class="font-bold text-lg">{{ $sub->store->name }}</h3>
                            <p class="text-gray-500 text-sm">{{ $sub->owner->name }} — {{ $sub->owner->email }}</p>
                            <p class="text-gray-400 text-sm">📱 {{ $sub->owner->phone ?? 'Sin teléfono' }}</p>
                        </div>

                        <div class="text-right">
                            <p class="text-2xl font-bold text-indigo-600">
                                ${{ number_format($sub->price, 0, ',', '.') }}
                            </p>
                            <p class="text-sm text-gray-500">{{ $plan['label'] }}</p>
                            <p class="text-xs text-gray-400">
                                Vencería: {{ now()->addMonths($plan['months'])->format('d/m/Y') }}
                            </p>
                        </div>
                    </div>

                    <div class="border-t mt-4 pt-4 flex gap-3 flex-wrap">
                        <form method="POST" action="{{ route('admin.subscriptions.confirm', $sub) }}">
                            @csrf
                            <button class="bg-green-600 text-white px-5 py-2 rounded-lg text-sm hover:bg-green-700 font-medium">
                                ✓ Confirmar pago y activar
                            </button>
                        </form>

                        <a href="https://wa.me/57{{ preg_replace('/\D/', '', $sub->owner->phone ?? '') }}"
                           target="_blank"
                           class="border border-green-500 text-green-600 px-5 py-2 rounded-lg text-sm hover:bg-green-50">
                            💬 WhatsApp
                        </a>

                        <form method="POST" action="{{ route('admin.subscriptions.cancel', $sub) }}">
                            @csrf
                            <button class="border border-red-300 text-red-400 px-5 py-2 rounded-lg text-sm hover:bg-red-50">
                                Rechazar
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection