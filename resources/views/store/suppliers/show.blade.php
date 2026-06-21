@extends('layouts.store')
@section('title', $supplier->name)
@section('page-title', $supplier->name)
@section('page-subtitle', 'Historial de compras y abonos')
@section('header-actions')
    <a href="{{ route('store.purchases.create') }}"
       class="bg-indigo-600 text-white px-3 py-2 lg:px-4 rounded-xl text-sm hover:bg-indigo-700 font-semibold flex items-center gap-1.5 transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        <span class="hidden sm:inline">Nueva compra</span>
        <span class="sm:hidden">Comprar</span>
    </a>
@endsection

@section('content')

    {{-- Info del proveedor --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 mb-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-xl font-bold flex-shrink-0">
                    {{ strtoupper(substr($supplier->name, 0, 1)) }}
                </div>
                <div>
                    <h3 class="font-bold text-lg text-slate-800">{{ $supplier->name }}</h3>
                    <p class="text-sm text-slate-400">
                        {{ $supplier->contact_name ?? 'Sin contacto' }}
                        @if($supplier->phone) · {{ $supplier->phone }}@endif
                    </p>
                    @if($supplier->address)
                        <p class="text-xs text-slate-400 mt-0.5">{{ $supplier->address }}</p>
                    @endif
                </div>
            </div>
            <div class="text-right">
                <p class="text-xs text-slate-400 uppercase tracking-wide">Deuda actual</p>
                @if($supplier->total_debt > 0)
                    <p class="text-2xl font-bold text-red-500">${{ number_format($supplier->total_debt, 0, ',', '.') }}</p>
                    <button onclick="openPaymentModal()"
                        class="mt-1 text-sm text-white bg-emerald-600 hover:bg-emerald-700 px-4 py-2 rounded-xl font-medium transition-colors">
                        💵 Registrar abono
                    </button>
                @else
                    <p class="text-2xl font-bold text-emerald-600">Al día ✓</p>
                @endif
            </div>
        </div>

        @if($supplier->notes)
            <div class="mt-4 pt-4 border-t border-slate-100 text-sm text-slate-500">
                <span class="font-medium text-slate-600">Notas:</span> {{ $supplier->notes }}
            </div>
        @endif

        <div class="mt-4">
            <a href="{{ route('store.suppliers.edit', $supplier) }}" class="text-indigo-600 hover:underline text-sm">
                ✏️ Editar información
            </a>
        </div>
    </div>

    {{-- Modal de abono --}}
    <div id="paymentModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4">
        <div class="bg-white rounded-t-3xl sm:rounded-2xl shadow-xl p-6 w-full sm:max-w-sm">
            <div class="w-10 h-1 bg-slate-200 rounded-full mx-auto mb-5 sm:hidden"></div>
            <h3 class="font-bold text-lg mb-0.5">Registrar abono</h3>
            <p class="text-slate-400 text-sm mb-5">
                Deuda actual: <strong class="text-red-500">${{ number_format($supplier->total_debt, 0, ',', '.') }}</strong>
            </p>
            <form method="POST" action="{{ route('store.supplierPayments.store', $supplier) }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1.5 text-slate-700">Monto a abonar</label>
                    <input type="number" name="amount" min="1" max="{{ $supplier->total_debt }}" step="1" required
                        placeholder="Ej: 100000"
                        class="w-full border-2 border-slate-200 rounded-xl px-3 py-3 text-lg font-bold focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1.5 text-slate-700">Método de pago</label>
                    <select name="method" class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-slate-50">
                        <option value="efectivo">Efectivo</option>
                        <option value="transferencia">Transferencia</option>
                        <option value="nequi">Nequi</option>
                        <option value="daviplata">Daviplata</option>
                    </select>
                </div>
                <div class="mb-5">
                    <label class="block text-sm font-medium mb-1.5 text-slate-700">Nota (opcional)</label>
                    <input type="text" name="notes" placeholder="Ej: Abono parcial pedido de mayo"
                        class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-slate-50">
                </div>
                <div class="flex gap-3">
                    <button type="submit"
                        class="flex-1 bg-emerald-600 text-white py-3 rounded-xl font-semibold hover:bg-emerald-700 transition-colors shadow-sm">
                        Registrar abono
                    </button>
                    <button type="button" onclick="closePaymentModal()"
                        class="flex-1 border border-slate-200 py-3 rounded-xl text-slate-600 hover:bg-slate-50 transition-colors">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Historial de compras --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 mb-5">
        <h3 class="font-semibold text-slate-800 mb-4">Historial de compras</h3>
        @forelse($purchases as $purchase)
            <a href="{{ route('store.purchases.show', $purchase) }}"
               class="block py-3 border-b border-slate-100 last:border-0 hover:bg-slate-50/70 -mx-2 px-2 rounded-lg transition-colors">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-slate-800">
                            Compra #{{ $purchase->id }}
                            <span class="text-xs {{ $purchase->type === 'credito' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-600' }} px-2 py-0.5 rounded-full ml-1">
                                {{ $purchase->type === 'credito' ? 'Crédito' : 'Contado' }}
                            </span>
                        </p>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $purchase->created_at->format('d/m/Y H:i') }} · {{ $purchase->items->count() }} producto(s)</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-slate-800">${{ number_format($purchase->total, 0, ',', '.') }}</p>
                        @if($purchase->status === 'pagada')
                            <span class="text-xs text-emerald-600">Pagada</span>
                        @elseif($purchase->status === 'parcial')
                            <span class="text-xs text-amber-600">Parcial</span>
                        @else
                            <span class="text-xs text-red-500">Pendiente</span>
                        @endif
                    </div>
                </div>
            </a>
        @empty
            <p class="text-center text-slate-400 py-6">Sin compras registradas a este proveedor.</p>
        @endforelse
    </div>

    {{-- Historial de abonos --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
        <h3 class="font-semibold text-slate-800 mb-4">Historial de abonos</h3>
        @forelse($payments as $payment)
            <div class="flex items-center justify-between py-3 border-b border-slate-100 last:border-0">
                <div>
                    <p class="text-sm font-medium text-slate-800">${{ number_format($payment->amount, 0, ',', '.') }}</p>
                    <p class="text-xs text-slate-400">{{ $payment->created_at->format('d/m/Y H:i') }} · {{ ucfirst($payment->method) }}</p>
                    @if($payment->notes)
                        <p class="text-xs text-slate-400 italic mt-0.5">{{ $payment->notes }}</p>
                    @endif
                </div>
                <span class="text-emerald-600 text-xs font-medium bg-emerald-50 px-2.5 py-1 rounded-full">Pagado</span>
            </div>
        @empty
            <p class="text-center text-slate-400 py-6">Sin abonos registrados.</p>
        @endforelse
    </div>

@endsection

@push('scripts')
<script>
function openPaymentModal() {
    document.getElementById('paymentModal').classList.remove('hidden');
}
function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closePaymentModal(); });
document.getElementById('paymentModal').addEventListener('click', function(e) {
    if (e.target === this) closePaymentModal();
});
</script>
@endpush