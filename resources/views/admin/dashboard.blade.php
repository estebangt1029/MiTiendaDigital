@extends('layouts.admin')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Resumen general del sistema')

@section('content')

    {{-- Stats principales --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 lg:p-5">
            <p class="text-xs text-slate-400 uppercase tracking-wide">Ingresos este mes</p>
            <p class="text-xl lg:text-2xl font-bold text-emerald-600 mt-1">${{ number_format($stats['monthly_revenue'], 0, ',', '.') }}</p>
            <p class="text-xs text-slate-400 mt-1">Total: ${{ number_format($stats['total_revenue'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 lg:p-5">
            <p class="text-xs text-slate-400 uppercase tracking-wide">Tiendas activas</p>
            <p class="text-xl lg:text-2xl font-bold text-indigo-600 mt-1">{{ $stats['active_stores'] }}</p>
            <p class="text-xs text-slate-400 mt-1">{{ $stats['inactive_stores'] }} inactivas</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 lg:p-5">
            <p class="text-xs text-slate-400 uppercase tracking-wide">Suscripciones activas</p>
            <p class="text-xl lg:text-2xl font-bold text-slate-800 mt-1">{{ $stats['active_subs'] }}</p>
            <p class="text-xs text-amber-500 mt-1">{{ $stats['expiring_soon'] }} vencen pronto</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 lg:p-5">
            <p class="text-xs text-slate-400 uppercase tracking-wide">Ventas hoy (sistema)</p>
            <p class="text-xl lg:text-2xl font-bold text-indigo-600 mt-1">${{ number_format($stats['sales_today'], 0, ',', '.') }}</p>
            <p class="text-xs text-slate-400 mt-1">Total: ${{ number_format($stats['total_sales'], 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Alerta: vencidas que aún no se han desactivado --}}
    @if(($stats['expired'] ?? 0) > 0)
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-5 flex items-center justify-between gap-3 text-sm">
            <span>🔴 <strong>{{ $stats['expired'] }}</strong> suscripción(es) vencida(s) que aún figuran como activas.</span>
            <a href="{{ route('admin.subscriptions.index') }}" class="text-xs underline whitespace-nowrap">Revisar →</a>
        </div>
    @endif

    {{-- Pendientes de confirmar --}}
    @if($pendingSubs->count() > 0)
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 mb-5">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-amber-700 text-sm lg:text-base">
                    ⚠ {{ $pendingSubs->count() }} pago(s) pendiente(s) de confirmar
                </h3>
                <a href="{{ route('admin.subscriptions.pending') }}"
                   class="text-xs text-amber-600 hover:underline whitespace-nowrap">Ver todos</a>
            </div>
            <div class="space-y-3">
                @foreach($pendingSubs->take(3) as $sub)
                    @php $plan = \App\Models\Subscription::plans()[$sub->plan]; @endphp
                    <div class="bg-white rounded-xl p-4 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
                        <div class="min-w-0">
                            <p class="font-medium text-sm text-slate-800">{{ $sub->store->name }}</p>
                            <p class="text-xs text-slate-400">{{ $sub->owner->name }} · {{ $sub->owner->phone ?? 'Sin teléfono' }}</p>
                            <p class="text-xs text-indigo-600 mt-0.5">{{ $plan['label'] }} — ${{ number_format($sub->price, 0, ',', '.') }}</p>
                        </div>
                        <div class="flex gap-2 flex-shrink-0">
                            <a href="https://wa.me/57{{ preg_replace('/\D/', '', $sub->owner->phone ?? '') }}"
                               target="_blank"
                               class="text-xs border border-emerald-300 text-emerald-600 px-3 py-1.5 rounded-lg hover:bg-emerald-50 transition-colors font-medium">
                                WhatsApp
                            </a>
                            <form method="POST" action="{{ route('admin.subscriptions.confirm', $sub) }}">
                                @csrf
                                <button class="text-xs bg-emerald-600 text-white px-3 py-1.5 rounded-lg hover:bg-emerald-700 transition-colors font-medium">
                                    ✓ Confirmar
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">

        {{-- Top tiendas por ventas --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 font-semibold text-slate-800 flex justify-between items-center">
                <span>Top tiendas por ventas</span>
                <a href="{{ route('admin.stores.index') }}" class="text-xs text-indigo-600 hover:underline font-normal">Ver todas</a>
            </div>
            <table class="w-full text-sm">
                <tbody class="divide-y divide-slate-100">
                    @forelse($topStores as $store)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-5 py-3.5">
                                <p class="font-medium text-slate-800">{{ $store->name }}</p>
                                <p class="text-xs text-slate-400">{{ $store->sales_count }} ventas</p>
                            </td>
                            <td class="px-5 py-3.5 text-right font-bold text-indigo-600">
                                ${{ number_format($store->sales_sum_total ?? 0, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="px-5 py-8 text-center text-slate-400 text-xs">Sin datos aún</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Suscripciones por vencer --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 font-semibold text-slate-800 flex justify-between items-center">
                <span>⏰ Vencen pronto</span>
                <a href="{{ route('admin.subscriptions.index') }}" class="text-xs text-indigo-600 hover:underline font-normal">Ver todas</a>
            </div>
            @if($expiringSoon->isEmpty())
                <p class="text-center text-slate-400 py-10 text-sm">No hay suscripciones por vencer. 🎉</p>
            @else
                <table class="w-full text-sm">
                    <tbody class="divide-y divide-slate-100">
                        @foreach($expiringSoon as $sub)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-5 py-3.5">
                                    <p class="font-medium text-slate-800">{{ $sub->store->name }}</p>
                                    <p class="text-xs text-slate-400">{{ $sub->owner->name }}</p>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <p class="text-xs text-amber-600 font-medium">
                                        Vence {{ \Carbon\Carbon::parse($sub->end_date)->diffForHumans() }}
                                    </p>
                                    <form method="POST" action="{{ route('admin.subscriptions.renew', $sub) }}" class="mt-1">
                                        @csrf
                                        <button class="text-xs text-emerald-600 hover:underline font-medium">+1 mes</button>
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
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
        <h3 class="font-semibold text-slate-800 mb-4">Ingresos por mes (últimos 6 meses)</h3>
        @if($revenueByMonth->isEmpty())
            <p class="text-center text-slate-400 py-10 text-sm">Sin datos aún.</p>
        @else
            <canvas id="revenueChart" height="80"></canvas>
        @endif
    </div>

@endsection

@push('scripts')
<script>
@if($revenueByMonth->isNotEmpty())
new Chart(document.getElementById('revenueChart'), {
    type: 'bar',
    data: {
        labels: @json($revenueByMonth->pluck('month')),
        datasets: [{
            label: 'Ingresos',
            data: @json($revenueByMonth->pluck('total')),
            backgroundColor: 'rgba(99,102,241,0.75)',
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