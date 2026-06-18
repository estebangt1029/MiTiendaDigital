@extends('layouts.store')
@section('title', 'Reportes')
@section('page-title', 'Reportes y estadísticas')
@section('page-subtitle', 'Análisis del rendimiento de la tienda')
@section('header-actions')
    <form method="GET" class="flex gap-2 items-center">
        <select name="period" onchange="this.form.submit()"
            class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="7"  {{ $period == 7  ? 'selected' : '' }}>Últimos 7 días</option>
            <option value="30" {{ $period == 30 ? 'selected' : '' }}>Últimos 30 días</option>
            <option value="60" {{ $period == 60 ? 'selected' : '' }}>Últimos 60 días</option>
            <option value="90" {{ $period == 90 ? 'selected' : '' }}>Últimos 90 días</option>
        </select>
    </form>
@endsection

@section('content')
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-xs text-gray-400 uppercase">Ventas totales</p>
            <p class="text-2xl font-bold text-indigo-600 mt-1">${{ number_format($summary['total_sales'], 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $summary['count_sales'] }} transacciones</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-xs text-gray-400 uppercase">Recaudado</p>
            <p class="text-2xl font-bold text-green-600 mt-1">${{ number_format($summary['total_paid'], 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">Efectivo + pagos</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-xs text-gray-400 uppercase">Fiado pendiente</p>
            <p class="text-2xl font-bold text-red-500 mt-1">${{ number_format($summary['pending_debt'], 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $summary['count_fiado'] }} ventas fiadas</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-xs text-gray-400 uppercase">Stock bajo</p>
            <p class="text-2xl font-bold text-amber-500 mt-1">{{ $summary['low_stock'] }}</p>
            <p class="text-xs text-gray-400 mt-1">de {{ $summary['total_products'] }} productos</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow p-5 mb-6">
        <h3 class="font-semibold mb-4">Ventas por día</h3>
        @if($salesByDay->isEmpty())
            <p class="text-center text-gray-400 py-8">Sin ventas en este período.</p>
        @else
            <canvas id="salesChart" height="80"></canvas>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow p-5">
            <h3 class="font-semibold mb-4">Productos más vendidos</h3>
            @if($topProducts->isEmpty())
                <p class="text-center text-gray-400 py-6">Sin datos.</p>
            @else
                <canvas id="productsChart" height="200"></canvas>
            @endif
        </div>
        <div class="bg-white rounded-xl shadow p-5">
            <h3 class="font-semibold mb-4">Ventas por categoría</h3>
            @if($byCategory->isEmpty())
                <p class="text-center text-gray-400 py-6">Sin datos.</p>
            @else
                <canvas id="categoryChart" height="200"></canvas>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow p-5">
            <h3 class="font-semibold mb-4">Clientes con mayor deuda</h3>
            @if($topDebtors->isEmpty())
                <p class="text-center text-gray-400 py-6">¡Todos los clientes están al día! ✓</p>
            @else
                <div class="space-y-3">
                    @foreach($topDebtors as $debtor)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold">
                                    {{ substr($debtor->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium">{{ $debtor->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $debtor->phone ?? 'Sin teléfono' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-red-500">${{ number_format($debtor->total_debt, 0, ',', '.') }}</p>
                                <a href="{{ route('store.customers.show', $debtor) }}"
                                   class="text-xs text-indigo-500 hover:underline">Ver detalle</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow p-5">
            <h3 class="font-semibold mb-4">Resumen general</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between py-2 border-b">
                    <span class="text-gray-500">Total clientes activos</span>
                    <span class="font-semibold">{{ $summary['total_customers'] }}</span>
                </div>
                <div class="flex justify-between py-2 border-b">
                    <span class="text-gray-500">Total productos activos</span>
                    <span class="font-semibold">{{ $summary['total_products'] }}</span>
                </div>
                <div class="flex justify-between py-2 border-b">
                    <span class="text-gray-500">Ventas en el período</span>
                    <span class="font-semibold">{{ $summary['count_sales'] }}</span>
                </div>
                <div class="flex justify-between py-2 border-b">
                    <span class="text-gray-500">Ventas fiadas</span>
                    <span class="font-semibold text-amber-500">{{ $summary['count_fiado'] }}</span>
                </div>
                <div class="flex justify-between py-2 border-b">
                    <span class="text-gray-500">Total recaudado</span>
                    <span class="font-semibold text-green-600">${{ number_format($summary['total_paid'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-gray-500">Deuda pendiente total</span>
                    <span class="font-semibold text-red-500">${{ number_format($summary['pending_debt'], 0, ',', '.') }}</span>
                </div>
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
                    backgroundColor: 'rgba(34,197,94,0.7)',
                    borderRadius: 6,
                }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'top' } },
            scales: { y: { beginAtZero: true, ticks: { callback: v => '$' + v.toLocaleString('es-CO') }}}
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
            datasets: [{ data: @json($byCategory->pluck('total')), backgroundColor: ['#6366f1','#22c55e','#f59e0b','#ef4444','#3b82f6','#8b5cf6','#14b8a6','#f97316'], borderWidth: 2 }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' }, tooltip: { callbacks: { label: ctx => ' $' + ctx.parsed.toLocaleString('es-CO') } } } }
    });
    @endif
</script>
@endpush