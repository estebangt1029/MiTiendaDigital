@extends('layouts.admin')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Resumen general del sistema')

@section('content')

    {{-- Stats principales --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-5">
            <p class="text-xs text-gray-400 uppercase">Ingresos este mes</p>
            <p class="text-2xl font-bold text-green-600 mt-1">${{ number_format($stats['monthly_revenue'], 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">Total: ${{ number_format($stats['total_revenue'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-5">
            <p class="text-xs text-gray-400 uppercase">Tiendas activas</p>
            <p class="text-2xl font-bold text-indigo-600 mt-1">{{ $stats['active_stores'] }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $stats['inactive_stores'] }} inactivas</p>
        </div>
        <div class="bg-white rounded-xl shadow p-5">
            <p class="text-xs text-gray-400 uppercase">Suscripciones activas</p>
            <p class="text-2xl font-bold mt-1">{{ $stats['active_subs'] }}</p>
            <p class="text-xs text-amber-500 mt-1">{{ $stats['expiring_soon'] }} vencen pronto</p>
        </div>
        <div class="bg-white rounded-xl shadow p-5">
            <p class="text-xs text-gray-400 uppercase">Ventas en sistema hoy</p>
            <p class="text-2xl font-bold text-indigo-600 mt-1">${{ number_format($stats['sales_today'], 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">Total: ${{ number_format($stats['total_sales'], 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Pendientes de confirmar --}}
    @if($pendingSubs->count() > 0)
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-5 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-amber-700">
                    ⚠ {{ $pendingSubs->count() }} pago(s) pendiente(s) de confirmar
                </h3>
                <a href="{{ route('admin.subscriptions.pending') }}"
                   class="text-xs text-amber-600 hover:underline">Ver todos</a>
            </div>
            <div class="space-y-3">
                @foreach($pendingSubs->take(3) as $sub)
                    @php $plan = \App\Models\Subscription::plans()[$sub->plan]; @endphp
                    <div class="bg-white rounded-lg p-4 flex justify-between items-center">
                        <div>
                            <p class="font-medium text-sm">{{ $sub->store->name }}</p>
                            <p class="text-xs text-gray-400">{{ $sub->owner->name }} · {{ $sub->owner->phone ?? 'Sin teléfono' }}</p>
                            <p class="text-xs text-indigo-600 mt-0.5">{{ $plan['label'] }} — ${{ number_format($sub->price, 0, ',', '.') }}</p>
                        </div>
                        <div class="flex gap-2">
                            <a href="https://wa.me/57{{ preg_replace('/\D/', '', $sub->owner->phone ?? '') }}"
                               target="_blank"
                               class="text-xs border border-green-400 text-green-600 px-3 py-1.5 rounded-lg hover:bg-green-50">
                                WhatsApp
                            </a>
                            <form method="POST" action="{{ route('admin.subscriptions.confirm', $sub) }}">
                                @csrf
                                <button class="text-xs bg-green-600 text-white px-3 py-1.5 rounded-lg hover:bg-green-700">
                                    ✓ Confirmar
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

        {{-- Top tiendas por ventas --}}
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="px-5 py-4 border-b font-semibold flex justify-between">
                <span>Top tiendas por ventas</span>
                <a href="{{ route('admin.stores.index') }}" class="text-xs text-indigo-600 hover:underline">Ver todas</a>
            </div>
            <table class="w-full text-sm">
                <tbody class="divide-y divide-gray-100">
                    @forelse($topStores as $store)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <p class="font-medium">{{ $store->name }}</p>
                                <p class="text-xs text-gray-400">{{ $store->sales_count }} ventas</p>
                            </td>
                            <td class="px-4 py-3 text-right font-bold text-indigo-600">
                                ${{ number_format($store->sales_sum_total ?? 0, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="px-4 py-6 text-center text-gray-400 text-xs">Sin datos aún</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Suscripciones por vencer --}}
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="px-5 py-4 border-b font-semibold flex justify-between">
                <span>⏰ Vencen pronto</span>
                <a href="{{ route('admin.subscriptions.index') }}" class="text-xs text-indigo-600 hover:underline">Ver todas</a>
            </div>
            @if($expiringSoon->isEmpty())
                <p class="text-center text-gray-400 py-8 text-sm">No hay suscripciones por vencer.</p>
            @else
                <table class="w-full text-sm">
                    <tbody class="divide-y divide-gray-100">
                        @foreach($expiringSoon as $sub)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <p class="font-medium">{{ $sub->store->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $sub->owner->name }}</p>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <p class="text-xs text-amber-600 font-medium">
                                        Vence {{ \Carbon\Carbon::parse($sub->end_date)->diffForHumans() }}
                                    </p>
                                    <form method="POST" action="{{ route('admin.subscriptions.renew', $sub) }}" class="mt-1">
                                        @csrf
                                        <button class="text-xs text-green-600 hover:underline">+1 mes</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    {{-- Ingresos por mes --}}
    <div class="bg-white rounded-xl shadow p-5">
        <h3 class="font-semibold mb-4">Ingresos por mes (últimos 6 meses)</h3>
        @if($revenueByMonth->isEmpty())
            <p class="text-center text-gray-400 py-6 text-sm">Sin datos aún.</p>
        @else
            <canvas id="revenueChart" height="80"></canvas>
        @endif
    </div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
@if($revenueByMonth->isNotEmpty())
new Chart(document.getElementById('revenueChart'), {
    type: 'bar',
    data: {
        labels: @json($revenueByMonth->pluck('month')),
        datasets: [{
            label: 'Ingresos',
            data: @json($revenueByMonth->pluck('total')),
            backgroundColor: 'rgba(99,102,241,0.7)',
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: {
            callback: v => '$' + v.toLocaleString('es-CO')
        }}}
    }
});
@endif
</script>
@endpush