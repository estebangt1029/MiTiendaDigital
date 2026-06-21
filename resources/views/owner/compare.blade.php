@extends('layouts.owner')
@section('title', 'Comparar Tiendas')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-6">

    {{-- Header con selector de período --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Comparar tiendas</h2>
            <p class="text-sm text-slate-400">Mira cómo va cada una de tus tiendas y qué puedes mejorar</p>
        </div>
        <form method="GET" class="flex items-center gap-2">
            <select name="period" onchange="this.form.submit()"
                class="border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                <option value="7"  {{ $period == 7  ? 'selected' : '' }}>Últimos 7 días</option>
                <option value="30" {{ $period == 30 ? 'selected' : '' }}>Últimos 30 días</option>
                <option value="60" {{ $period == 60 ? 'selected' : '' }}>Últimos 60 días</option>
                <option value="90" {{ $period == 90 ? 'selected' : '' }}>Últimos 90 días</option>
            </select>
        </form>
    </div>

    @if($storeStats->isEmpty())
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-12 text-center text-slate-400">
            <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <p class="text-lg mb-1">Necesitas al menos una tienda activa para comparar</p>
            <a href="{{ route('owner.stores.index') }}" class="text-indigo-600 hover:underline text-sm">Ir a Mis tiendas →</a>
        </div>
    @else

        {{-- Si solo hay 1 tienda, avisamos que la comparación es más útil con 2+ --}}
        @if($storeStats->count() === 1)
            <div class="bg-indigo-50 border border-indigo-200 text-indigo-700 px-4 py-3 rounded-xl mb-5 text-sm">
                💡 Tienes una sola tienda activa. Cuando actives una segunda, podrás compararlas lado a lado.
            </div>
        @endif

        {{-- ══ Tarjetas resumen por tienda ══ --}}
        <div class="grid grid-cols-1 {{ $storeStats->count() > 1 ? 'lg:grid-cols-2' : '' }} gap-4 mb-6">
            @foreach($storeStats as $stat)
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 {{ $stat->id === $bestStoreId && $storeStats->count() > 1 ? 'ring-2 ring-emerald-400' : '' }}">
                    <div class="flex items-start justify-between gap-3 mb-4">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-bold text-lg text-slate-800">{{ $stat->name }}</h3>
                                @if($stat->id === $bestStoreId && $storeStats->count() > 1)
                                    <span class="bg-emerald-100 text-emerald-700 text-xs px-2 py-0.5 rounded-full font-medium">🏆 Top ventas</span>
                                @elseif($stat->id === $worstStoreId && $storeStats->count() > 1)
                                    <span class="bg-amber-100 text-amber-700 text-xs px-2 py-0.5 rounded-full font-medium">A mejorar</span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-400 mt-0.5">{{ $stat->sales_count }} ventas en el período</p>
                        </div>
                        <a href="{{ route('owner.stores.enter', $stat->id) }}"
                           class="text-xs text-indigo-600 hover:underline whitespace-nowrap">Entrar →</a>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-slate-50 rounded-xl p-3">
                            <p class="text-xs text-slate-400">Ventas totales</p>
                            <p class="font-bold text-slate-800 text-lg">${{ number_format($stat->total_sales, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-3">
                            <p class="text-xs text-slate-400">Ticket promedio</p>
                            <p class="font-bold text-slate-800 text-lg">${{ number_format($stat->avg_ticket, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-3">
                            <p class="text-xs text-slate-400">Ganancia estimada</p>
                            <p class="font-bold text-emerald-600 text-lg">${{ number_format($stat->estimated_profit, 0, ',', '.') }}</p>
                            <p class="text-xs text-slate-400">{{ $stat->margin_pct }}% margen</p>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-3">
                            <p class="text-xs text-slate-400">Fiado pendiente</p>
                            <p class="font-bold text-red-500 text-lg">${{ number_format($stat->total_pending, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="mt-3 pt-3 border-t border-slate-100 space-y-2 text-sm">
                        @if($stat->top_product_name)
                            <div class="flex justify-between">
                                <span class="text-slate-500">Producto top</span>
                                <span class="font-medium text-slate-700">{{ $stat->top_product_name }} ({{ $stat->top_product_qty }} und)</span>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-slate-500">Productos / stock bajo</span>
                            <span class="font-medium {{ $stat->low_stock_count > 0 ? 'text-amber-600' : 'text-slate-700' }}">
                                {{ $stat->total_products }} / {{ $stat->low_stock_count }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Clientes activos</span>
                            <span class="font-medium text-slate-700">{{ $stat->total_customers }}</span>
                        </div>
                        @if($stat->total_supplier_debt > 0)
                            <div class="flex justify-between">
                                <span class="text-slate-500">Debes a proveedores</span>
                                <span class="font-medium text-amber-600">${{ number_format($stat->total_supplier_debt, 0, ',', '.') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        @if($storeStats->count() > 1)
            {{-- ══ Gráfica comparativa de ventas ══ --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 mb-6">
                <h3 class="font-semibold text-slate-800 mb-4">Ventas por tienda</h3>
                <canvas id="compareChart" height="90"></canvas>
            </div>

            {{-- ══ Tabla comparativa completa ══ --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden overflow-x-auto">
                <table class="w-full text-sm min-w-[640px]">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-xs border-b border-slate-100">
                        <tr>
                            <th class="px-5 py-3.5 text-left">Tienda</th>
                            <th class="px-5 py-3.5 text-right"># Ventas</th>
                            <th class="px-5 py-3.5 text-right">Total vendido</th>
                            <th class="px-5 py-3.5 text-right">Ganancia est.</th>
                            <th class="px-5 py-3.5 text-right">Margen</th>
                            <th class="px-5 py-3.5 text-right">Fiado pendiente</th>
                            <th class="px-5 py-3.5 text-right">Stock bajo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($storeStats as $stat)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-5 py-3.5 font-medium text-slate-800">{{ $stat->name }}</td>
                                <td class="px-5 py-3.5 text-right text-slate-600">{{ $stat->sales_count }}</td>
                                <td class="px-5 py-3.5 text-right font-semibold text-slate-800">${{ number_format($stat->total_sales, 0, ',', '.') }}</td>
                                <td class="px-5 py-3.5 text-right text-emerald-600 font-medium">${{ number_format($stat->estimated_profit, 0, ',', '.') }}</td>
                                <td class="px-5 py-3.5 text-right text-slate-600">{{ $stat->margin_pct }}%</td>
                                <td class="px-5 py-3.5 text-right {{ $stat->total_pending > 0 ? 'text-red-500 font-medium' : 'text-slate-300' }}">
                                    ${{ number_format($stat->total_pending, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-3.5 text-right {{ $stat->low_stock_count > 0 ? 'text-amber-600 font-medium' : 'text-slate-300' }}">
                                    {{ $stat->low_stock_count }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <p class="text-xs text-slate-400 text-center mt-6">
            💡 La ganancia estimada se calcula con el costo actual de cada producto, no el costo histórico al momento de cada venta.
        </p>
    @endif
</div>
@endsection

@if($storeStats->count() > 1)
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('compareChart'), {
    type: 'bar',
    data: {
        labels: @json($storeStats->pluck('name')),
        datasets: [
            {
                label: 'Total vendido',
                data: @json($storeStats->pluck('total_sales')),
                backgroundColor: 'rgba(99,102,241,0.75)',
                borderRadius: 6,
            },
            {
                label: 'Ganancia estimada',
                data: @json($storeStats->pluck('estimated_profit')),
                backgroundColor: 'rgba(16,185,129,0.75)',
                borderRadius: 6,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top' } },
        scales: { y: { beginAtZero: true, ticks: { callback: v => '$' + v.toLocaleString('es-CO') } } }
    }
});
</script>
@endpush
@endif