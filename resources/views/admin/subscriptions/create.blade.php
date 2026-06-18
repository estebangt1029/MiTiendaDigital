@extends('layouts.admin')
@section('title', 'Nueva Suscripción')
@section('page-title', 'Nueva suscripción')

@section('content')
    <div class="max-w-lg">
        <div class="bg-white rounded-xl shadow p-8">
            <form method="POST" action="{{ route('admin.subscriptions.store') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Dueño *</label>
                    <select name="owner_id" required
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-800">
                        <option value="">Seleccionar dueño...</option>
                        @foreach($owners as $owner)
                            <option value="{{ $owner->id }}">{{ $owner->name }} — {{ $owner->email }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Tienda *</label>
                    <select name="store_id" required
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-800">
                        <option value="">Seleccionar tienda...</option>
                        @foreach($stores as $store)
                            <option value="{{ $store->id }}">{{ $store->name }} ({{ $store->owner->name }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Plan *</label>
                        <select name="plan" class="w-full border rounded-lg px-3 py-2 focus:outline-none">
                            <option value="basic">Basic</option>
                            <option value="pro">Pro</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Precio mensual *</label>
                        <input type="number" name="price" min="0" required
                            class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-800"
                            placeholder="50000">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium mb-1">Fecha inicio *</label>
                        <input type="date" name="start_date" value="{{ date('Y-m-d') }}" required
                            class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-800">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Fecha fin *</label>
                        <input type="date" name="end_date" value="{{ date('Y-m-d', strtotime('+1 month')) }}" required
                            class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-800">
                    </div>
                </div>
                <button type="submit"
                    class="w-full bg-gray-900 text-white py-2 rounded-lg hover:bg-gray-700 font-medium">
                    Crear suscripción
                </button>
            </form>
        </div>
    </div>
@endsection