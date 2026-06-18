@extends('layouts.owner')
@section('title', 'Mis Tiendas')

@section('content')
<div class="max-w-5xl mx-auto mt-10 px-4 pb-10">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold">Mis tiendas</h2>
        <a href="{{ route('owner.stores.create') }}"
           class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 font-medium">
            + Nueva tienda
        </a>
    </div>

    @if($stores->isEmpty())
        <div class="bg-white rounded-xl shadow p-10 text-center text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
            </svg>
            <p class="text-lg mb-2">No tienes tiendas aún</p>
            <a href="{{ route('owner.stores.create') }}" class="text-indigo-600 hover:underline text-sm">
                Crea tu primera tienda →
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($stores as $store)
                <div class="bg-white rounded-xl shadow p-6 flex flex-col gap-3 hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-bold text-lg">{{ $store->name }}</h3>
                            <p class="text-gray-400 text-sm mt-0.5">{{ $store->address ?? 'Sin dirección' }}</p>
                            <p class="text-gray-400 text-sm">{{ $store->phone ?? 'Sin teléfono' }}</p>
                        </div>
                        <span class="text-xs px-2 py-1 rounded-full {{ $store->active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $store->active ? 'Activa' : 'Inactiva' }}
                        </span>
                    </div>
                    <div class="flex gap-4 text-sm text-gray-400">
                        <span>📦 {{ $store->products_count }} productos</span>
                        <span>👥 {{ $store->customers_count }} clientes</span>
                    </div>
                    <div class="flex gap-2 mt-1">
                        <a href="{{ route('owner.stores.enter', $store->id) }}"
                           class="flex-1 text-center bg-indigo-600 text-white px-3 py-2 rounded-lg text-sm hover:bg-indigo-700 font-medium">
                            Entrar
                        </a>
                        <a href="{{ route('owner.stores.edit', $store) }}"
                           class="flex-1 text-center border border-gray-200 text-gray-600 px-3 py-2 rounded-lg text-sm hover:bg-gray-50">
                            Editar
                        </a>
                        <form method="POST" action="{{ route('owner.stores.destroy', $store) }}"
                              onsubmit="return confirm('¿Desactivar esta tienda?')">
                            @csrf @method('DELETE')
                            <button class="border border-red-200 text-red-400 px-3 py-2 rounded-lg text-sm hover:bg-red-50">
                                Desactivar
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection