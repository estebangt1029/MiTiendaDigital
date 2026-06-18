@extends('layouts.admin')
@section('title', 'Suscripciones')
@section('page-title', 'Suscripciones')
@section('page-subtitle', 'Control de pagos y vencimientos')
@section('header-actions')
    <a href="{{ route('admin.subscriptions.create') }}"
       class="bg-gray-900 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-700">
        + Nueva suscripción
    </a>
@endsection

@section('content')
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">Tienda</th>
                    <th class="px-4 py-3 text-left">Dueño</th>
                    <th class="px-4 py-3 text-center">Plan</th>
                    <th class="px-4 py-3 text-right">Precio</th>
                    <th class="px-4 py-3 text-center">Vencimiento</th>
                    <th class="px-4 py-3 text-center">Estado</th>
                    <th class="px-4 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($subscriptions as $sub)
                    @php
                        $expired = \Carbon\Carbon::parse($sub->end_date)->isPast();
                        $soon    = !$expired && \Carbon\Carbon::parse($sub->end_date)->diffInDays(now()) <= 7;
                    @endphp
                    <tr class="hover:bg-gray-50 {{ $expired ? 'bg-red-50' : ($soon ? 'bg-amber-50' : '') }}">
                        <td class="px-4 py-3 font-medium">{{ $sub->store->name }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $sub->owner->name }}</td>
                        <td class="px-4 py-3 text-center capitalize">{{ $sub->plan }}</td>
                        <td class="px-4 py-3 text-right">${{ number_format($sub->price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">
                            <p class="{{ $expired ? 'text-red-600 font-bold' : ($soon ? 'text-amber-600 font-medium' : 'text-gray-600') }}">
                                {{ \Carbon\Carbon::parse($sub->end_date)->format('d/m/Y') }}
                            </p>
                            <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($sub->end_date)->diffForHumans() }}</p>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($expired)
                                <span class="bg-red-100 text-red-700 px-2 py-0.5 rounded-full text-xs">Vencida</span>
                            @elseif($soon)
                                <span class="bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full text-xs">Por vencer</span>
                            @elseif($sub->active)
                                <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-xs">Activa</span>
                            @else
                                <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full text-xs">Cancelada</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center flex justify-center gap-2">
                            <form method="POST" action="{{ route('admin.subscriptions.renew', $sub) }}">
                                @csrf
                                <button class="text-green-600 hover:underline text-xs">+1 mes</button>
                            </form>
                            @if($sub->active)
                                <form method="POST" action="{{ route('admin.subscriptions.cancel', $sub) }}">
                                    @csrf
                                    <button class="text-red-400 hover:text-red-600 text-xs">Cancelar</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-400">No hay suscripciones.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection