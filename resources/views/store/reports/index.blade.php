@extends('layouts.store')
@section('title', 'Reportes')
@section('page-title', 'Reportes')
@section('page-subtitle', 'Análisis del rendimiento de la tienda')
@section('header-actions')
    <form method="GET">
        <select name="period" onchange="this.form.submit()"
            class="border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-slate-50">
            <option value="7"  {{ $period == 7  ? 'selected' : '' }}>7 días</option>
            <option value="30" {{ $period == 30 ? 'selected' : '' }}>30 días</option>
            <option value="60" {{ $period == 60 ? 'selected' : '' }}>60 días</option>
            <option value="90" {{ $period == 90 ? 'selected' : '' }}>90 días</option>
        </select>
    </form>
@endsection

@section('content')

    {{-- KPIs --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4">
            <p class="text-xs text-slate-400 uppercase tracking-wide">Ventas totales</p>
            <p class="text-xl font-bold text-indigo-600 mt-1">${{ number_format($summary['total_sales'], 0, ',', '.') }}</p>
            <p class="text-xs text-slate-400 mt-0.5">{{ $summary['count_sales'] }} transacciones</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4">
            <p class="text-xs text-slate-400 uppercase tracking-wide">Recaudado</p>
            <p class="text-xl font-bold text-emerald-600 mt-1">${{ number_format($summary['total_paid'], 0, ',', '.') }}</p>
            <p class="text-xs text-slate-400 mt-0.5">Efectivo + pagos</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4">
            <p class="text-xs text-slate-400 uppercase tracking-wide">Fiado pendiente</p>
            <p class="text-xl font-bold text-red-500 mt-1">${{ number_format($summary['pending_debt'], 0, ',', '.') }}</p>
            <p class="text-xs text-slate-400 mt-0.5">{{ $summary['count_fiado'] }} ventas fiadas</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4">
            <p class="text-xs text-slate-400 uppercase tracking-wide">Stock bajo</p>
            <p class="text-xl font-bold text-amber-500 mt-1">{{ $summary['low_stock'] }}</p>
            <p class="text-xs text-slate-400 mt-0.5">de {{ $summary['total_products'] }} productos</p>
        </div>
    </div>

    {{-- Gráfica ventas por día --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 mb-5">
        <h3 class="font-semibold text-slate-800 mb-4">Ventas por día</h3>
        @if($salesByDay->isEmpty())
            <div class="py-10 text-center text-slate-400">
                <p class="text-3xl mb-2">📊</p>
                <p>Sin ventas en este período.</p>
            </div>
        @else
            <canvas id="salesChart" height="80"></canvas>
        @endif
    </div>

    {{-- Gráficas secundarias --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-5">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
            <h3 class="font-semibold text-slate-800 mb-4">Productos más vendidos</h3>
            @if($topProducts->isEmpty())
                <p class="text-center text-slate-400 py-6">Sin datos.</p>
            @else
                <canvas id="productsChart" height="200"></canvas>
            @endif
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
            <h3 class="font-semibold text-slate-800 mb-4">Ventas por categoría</h3>
            @if($byCategory->isEmpty())
                <p class="text-center text-slate-400 py-6">Sin datos.</p>
            @else
                <canvas id="categoryChart" height="200"></canvas>
            @endif
        </div>
    </div>

    {{-- Deudores + Resumen --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
            <h3 class="font-semibold text-slate-800 mb-4">Clientes con mayor deuda</h3>
            @if($topDebtors->isEmpty())
                <div class="py-6 text-center text-slate-400">
                    <p class="text-2xl mb-2">✓</p>
                    <p>¡Todos los clientes están al día!</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($topDebtors as $debtor)
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-sm font-bold flex-shrink-0">
                                    {{ substr($debtor->name, 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-slate-800 truncate">{{ $debtor->name }}</p>
                                    <p class="text-xs text-slate-400">{{ $debtor->phone ?? 'Sin teléfono' }}</p>
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <p class="font-bold text-red-500">${{ number_format($debtor->total_debt, 0, ',', '.') }}</p>
                                <a href="{{ route('store.customers.show', $debtor) }}"
                                   class="text-xs text-indigo-500 hover:underline">Ver detalle</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
            <h3 class="font-semibold text-slate-800 mb-4">Resumen general</h3>
            <div class="space-y-0">
                @foreach([
                    ['label' => 'Clientes activos',     'value' => $summary['total_customers'],                                          'color' => ''],
                    ['label' => 'Productos activos',     'value' => $summary['total_products'],                                          'color' => ''],
                    ['label' => 'Ventas en el período',  'value' => $summary['count_sales'],                                             'color' => ''],
                    ['label' => 'Ventas fiadas',         'value' => $summary['count_fiado'],                                             'color' => 'text-amber-500'],
                    ['label' => 'Total recaudado',       'value' => '$'.number_format($summary['total_paid'], 0, ',', '.'),               'color' => 'text-emerald-600'],
                    ['label' => 'Deuda pendiente total', 'value' => '$'.number_format($summary['pending_debt'], 0, ',', '.'),             'color' => 'text-red-500'],
                ] as $item)
                    <div class="flex justify-between items-center py-2.5 border-b border-slate-100 last:border-0">
                        <span class="text-sm text-slate-500">{{ $item['label'] }}</span>
                        <span class="font-semibold text-sm {{ $item['color'] ?: 'text-slate-800' }}">{{ $item['value'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    @if($salesByDay->isNotEmpty())
    new Chart(document.getElementById('salesChart'), {
        type: 'bar',
        data: {
            labels: @json($salesByDay->pluck('date')),
            datasets: [
                {
                    label: 'Total vendido',
                    data: @json($salesByDay->pluck('total')),
                    backgroundColor: 'rgba(99,102,241,0.7)',
                    borderRadius: 6,
                },
                {
                    label: 'Recaudado',
                    data: @json($salesByDay->pluck('paid')),
                    backgroundColor: 'rgba(16,185,129,0.7)',
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
    @endif

    @if($topProducts->isNotEmpty())
    new Chart(document.getElementById('productsChart'), {
        type: 'bar',
        indexAxis: 'y',
        data: {
            labels: @json($topProducts->pluck('name')),
            datasets: [{ label: 'Unidades', data: @json($topProducts->pluck('total_qty')), backgroundColor: 'rgba(99,102,241,0.7)', borderRadius: 4 }]
        },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true } } }
    });
    @endif

    @if($byCategory->isNotEmpty())
    new Chart(document.getElementById('categoryChart'), {
        type: 'doughnut',
        data: {
            labels: @json($byCategory->pluck('category')),
            datasets: [{ data: @json($byCategory->pluck('total')), backgroundColor: ['#6366f1','#10b981','#f59e0b','#ef4444','#3b82f6','#8b5cf6','#14b8a6','#f97316'], borderWidth: 2 }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' }, tooltip: { callbacks: { label: ctx => ' $' + ctx.parsed.toLocaleString('es-CO') } } } }
    });
    @endif
</script>
@endpush
