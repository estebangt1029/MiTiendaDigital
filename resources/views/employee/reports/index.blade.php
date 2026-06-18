@extends('layouts.storeuser')
@section('title', 'Reportes')
@section('page-title', 'Reportes del día')

@section('content')
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-xs text-gray-400 uppercase">Ventas hoy</p>
            <p class="text-2xl font-bold text-green-600 mt-1">${{ number_format($totalHoy, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $ventasHoy }} transacciones</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-xs text-gray-400 uppercase">Pendientes pago</p>
            <p class="text-2xl font-bold text-red-500 mt-1">{{ $pendientes }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-xs text-gray-400 uppercase">Deuda total clientes</p>
            <p class="text-2xl font-bold text-red-500 mt-1">${{ number_format($totalDeuda, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-xs text-gray-400 uppercase">Stock bajo</p>
            <p class="text-2xl font-bold text-amber-500 mt-1">{{ $lowStock }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow p-5">
        <h3 class="font-semibold mb-4">Ventas últimos 7 días</h3>
        @if($salesByDay->isEmpty())
            <p class="text-center text-gray-400 py-8">Sin ventas en este período.</p>
        @else
            <canvas id="salesChart" height="80"></canvas>
        @endif
    </div>
@endsection

@push('scripts')
<script>
    @if($salesByDay->isNotEmpty())
    new Chart(document.getElementById('salesChart'), {
        type: 'bar',
        data: {
            labels: @json($salesByDay->pluck('date')),
            datasets: [{
                label: 'Total vendido',
                data: @json($salesByDay->pluck('total')),
                backgroundColor: 'rgba(34,197,94,0.7)',
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { callback: v => '$' + v.toLocaleString('es-CO') }}}
        }
    });
    @endif
</script>
@endpush