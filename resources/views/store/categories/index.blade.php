@extends('layouts.store')
@section('title', 'Categorías')
@section('page-title', 'Categorías')
@section('page-subtitle', 'Organiza tus productos por categoría')

@section('content')
    <div class="max-w-2xl space-y-4">

        {{-- Crear categoría --}}
        <div class="bg-white rounded-xl shadow p-5">
            <h3 class="font-semibold mb-4">Nueva categoría</h3>
            <form method="POST" action="{{ route('store.categories.store') }}" class="flex gap-3 items-end">
                @csrf
                <div class="flex-1">
                    <label class="block text-xs text-gray-500 mb-1">Nombre *</label>
                    <input type="text" name="name" placeholder="Ej: Bebidas, Aseo, Snacks..." required
                        class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Color</label>
                    <input type="color" name="color" value="#6366f1"
                        class="border rounded-lg w-10 h-10 cursor-pointer p-0.5">
                </div>
                <button type="submit"
                    class="bg-indigo-600 text-white px-5 py-2 rounded-lg text-sm hover:bg-indigo-700 font-medium h-10">
                    Crear
                </button>
            </form>
        </div>

        {{-- Buscador --}}
        <div class="relative">
            <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" id="catSearch" placeholder="Buscar categoría..."
                oninput="filterCats()"
                class="w-full border rounded-lg pl-9 pr-3 py-2 text-sm bg-white shadow focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        {{-- Lista de categorías --}}
        <div class="bg-white rounded-xl shadow overflow-hidden">
            @forelse($categories as $cat)
                <div class="cat-row flex items-center justify-between px-4 py-3 border-b last:border-0 hover:bg-gray-50"
                     data-name="{{ strtolower($cat->name) }}">
                    <div class="flex items-center gap-3">
                        <span class="w-5 h-5 rounded-full flex-shrink-0" style="background:{{ $cat->color }}"></span>
                        <div>
                            <p class="font-medium text-sm">{{ $cat->name }}</p>
                            <p class="text-xs text-gray-400">{{ $cat->products_count }} producto(s)</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        {{-- Editar inline --}}
                        <button onclick="toggleEdit({{ $cat->id }})"
                            class="text-xs text-indigo-500 hover:underline">Editar</button>
                        <form method="POST" action="{{ route('store.categories.destroy', $cat) }}"
                              onsubmit="return confirm('¿Eliminar {{ $cat->name }}?')">
                            @csrf @method('DELETE')
                            <button class="text-xs text-red-400 hover:text-red-600">Eliminar</button>
                        </form>
                    </div>
                </div>
                {{-- Fila de edición inline --}}
                <div id="edit_{{ $cat->id }}" class="hidden bg-indigo-50 px-4 py-3 border-b">
                    <form method="POST" action="{{ route('store.categories.update', $cat) }}"
                          class="flex gap-3 items-center">
                        @csrf @method('PUT')
                        <input type="text" name="name" value="{{ $cat->name }}" required
                            class="flex-1 border rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <input type="color" name="color" value="{{ $cat->color }}"
                            class="border rounded w-8 h-8 cursor-pointer">
                        <button type="submit" class="bg-indigo-600 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-indigo-700">
                            Guardar
                        </button>
                        <button type="button" onclick="toggleEdit({{ $cat->id }})"
                            class="text-gray-400 hover:text-gray-600 text-sm">Cancelar</button>
                    </form>
                </div>
            @empty
                <p class="text-center text-gray-400 py-10 text-sm">No hay categorías aún. Crea la primera arriba.</p>
            @endforelse
            <div id="noCatResults" class="hidden text-center text-gray-400 py-8 text-sm">No se encontraron categorías.</div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function filterCats() {
    const search = document.getElementById('catSearch').value.toLowerCase();
    const rows   = document.querySelectorAll('.cat-row');
    let visible  = 0;
    rows.forEach(row => {
        const match = !search || row.dataset.name.includes(search);
        row.classList.toggle('hidden', !match);
        const editRow = row.nextElementSibling;
        if (editRow && editRow.id && editRow.id.startsWith('edit_')) {
            editRow.classList.add('hidden');
        }
        if (match) visible++;
    });
    document.getElementById('noCatResults').classList.toggle('hidden', visible > 0);
}

function toggleEdit(id) {
    const el = document.getElementById('edit_' + id);
    el.classList.toggle('hidden');
    if (!el.classList.contains('hidden')) {
        el.querySelector('input[type="text"]').focus();
    }
}
</script>
@endpush