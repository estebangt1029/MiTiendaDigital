@extends('layouts.owner')
@section('title', 'Mis Tiendas')

@section('content')
<div class="max-w-5xl mx-auto mt-8 px-4 pb-10">

    {{-- Tiendas pendientes de pago --}}
    @if($pendingSubs->count() > 0)
        @foreach($pendingSubs as $sub)
            <div class="bg-amber-950 border border-amber-700 rounded-2xl p-6 mb-6">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-amber-600 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-amber-300 font-bold text-lg">
                            "{{ $sub->store->name }}" — Pendiente de activación
                        </h3>
                        <p class="text-amber-400 text-sm mt-1">
                            Tu tienda está lista pero necesita activarse. Realiza el pago para comenzar a usarla.
                        </p>

                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-amber-900/50 rounded-xl p-4">
                                <p class="text-amber-300 text-xs font-semibold uppercase mb-2">Resumen de tu plan</p>
                                <p class="text-white font-bold">
                                    {{ \App\Models\Subscription::plans()[$sub->plan]['label'] }}
                                </p>
                                <p class="text-amber-300 text-2xl font-black mt-1">
                                    ${{ number_format($sub->price, 0, ',', '.') }} COP
                                </p>
                            </div>
                            <div class="bg-amber-900/50 rounded-xl p-4">
                                <p class="text-amber-300 text-xs font-semibold uppercase mb-2">¿Cómo pagar?</p>
                                <ol class="text-amber-200 text-sm space-y-1 list-decimal list-inside">
                                    <li>Paga por Nequi o transferencia</li>
                                    <li>Envía el comprobante por WhatsApp</li>
                                    <li>Te activamos en minutos</li>
                                </ol>
                            </div>
                        </div>

                        <div class="mt-4 flex gap-3 flex-wrap">
                            <a href="https://wa.me/57{{ config('app.admin_phone') }}?text=Hola, acabo de registrarme en MiTiendaDigital. Mi tienda es {{ urlencode($sub->store->name) }} y quiero activar mi plan de {{ \App\Models\Subscription::plans()[$sub->plan]['label'] }} por ${{ number_format($sub->price, 0, ',', '.') }} COP."
                               target="_blank"
                               class="bg-green-600 hover:bg-green-500 text-white px-5 py-2.5 rounded-xl text-sm font-semibold flex items-center gap-2">
                                💬 Enviar comprobante por WhatsApp
                            </a>
                            <button onclick="window.location.reload()"
                               class="border border-amber-600 text-amber-300 hover:bg-amber-900 px-5 py-2.5 rounded-xl text-sm font-semibold">
                                🔄 Verificar activación
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    {{-- Tiendas activas --}}
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold">Mis tiendas</h2>
        <a href="{{ route('owner.stores.create') }}"
           class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 font-medium">
            + Nueva tienda
        </a>
    </div>

    @if($activeStores->isEmpty() && $pendingSubs->isEmpty())
        <div class="bg-white rounded-xl shadow p-10 text-center text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
            </svg>
            <p class="text-lg mb-2">No tienes tiendas activas</p>
            <a href="{{ route('owner.stores.create') }}" class="text-indigo-600 hover:underline text-sm">
                Crea tu primera tienda →
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($activeStores as $store)
                <div class="bg-white rounded-xl shadow p-6 flex flex-col gap-3 hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-bold text-lg">{{ $store->name }}</h3>
                            <p class="text-gray-400 text-sm mt-0.5">{{ $store->address ?? 'Sin dirección' }}</p>
                        </div>
                        <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700">Activa</span>
                    </div>
                    <div class="flex gap-4 text-sm text-gray-400">
                        <span>📦 {{ $store->products_count }} productos</span>
                        <span>👥 {{ $store->customers_count }} clientes</span>
                    </div>

                    {{-- Días restantes de suscripción --}}
                    @php
                        $sub = $store->subscriptions()->where('active', true)->latest()->first();
                    @endphp
                    @if($sub)
                        @php $daysLeft = now()->diffInDays($sub->end_date, false); @endphp
                        <div class="text-xs {{ $daysLeft <= 7 ? 'text-amber-600' : 'text-gray-400' }}">
                            📅 Suscripción vence {{ \Carbon\Carbon::parse($sub->end_date)->format('d/m/Y') }}
                            ({{ $daysLeft > 0 ? $daysLeft.' días' : 'vencida' }})
                        </div>
                    @endif

                    <div class="flex gap-2 mt-1">
                        <a href="{{ route('owner.stores.enter', $store->id) }}"
                           class="flex-1 text-center bg-indigo-600 text-white px-3 py-2 rounded-lg text-sm hover:bg-indigo-700 font-medium">
                            Entrar
                        </a>
                        <a href="{{ route('owner.stores.edit', $store) }}"
                           class="flex-1 text-center border border-gray-200 text-gray-600 px-3 py-2 rounded-lg text-sm hover:bg-gray-50">
                            Editar
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection