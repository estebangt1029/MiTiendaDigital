@extends('layouts.store')
@section('title', 'Compra #'.$purchase->id)
@section('page-title', 'Compra #'.$purchase->id)
@section('page-subtitle', $purchase->created_at->format('d/m/Y H:i'))

@section('content')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Columna izquierda: productos comprados --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
                <h3 class="font-semibold text-slate-800 mb-4">Productos comprados</h3>
                <div class="space-y-3">
                    @foreach($purchase->items as $item)
                        <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-800">{{ $item->product->name ?? 'Producto eliminado' }}</p>
                                <p class="text-xs text-slate-400">{{ $item->quantity }} unidad(es) × ${{ number_format($item->unit_cost, 0, ',', '.') }}</p>
                            </div>
                            <p class="font-semibold text-slate-800">${{ number_format($item->subtotal, 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 pt-4 border-t border-slate-200 flex justify-between items-center">
                    <span class="font-semibold text-slate-700">Total</span>
                    <span class="text-xl font-bold text-slate-800">${{ number_format($purchase->total, 0, ',', '.') }}</span>
                </div>

                @if($purchase->update_product_cost)
                    <div class="mt-3 bg-indigo-50 border border-indigo-100 text-indigo-700 px-3 py-2 rounded-lg text-xs">
                        ✓ El costo de estos productos se actualizó con los valores de esta compra.
                    </div>
                @endif
            </div>

            @if($purchase->notes)
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
                    <h3 class="font-semibold text-slate-800 mb-2">Notas</h3>
                    <p class="text-sm text-slate-500">{{ $purchase->notes }}</p>
                </div>
            @endif

            {{-- Abonos asociados a esta compra puntual --}}
            @if($purchase->payments->isNotEmpty())
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
                    <h3 class="font-semibold text-slate-800 mb-4">Abonos a esta compra</h3>
                    <div class="space-y-3">
                        @foreach($purchase->payments as $payment)
                            <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
                                <div>
                                    <p class="text-sm font-medium text-slate-800">${{ number_format($payment->amount, 0, ',', '.') }}</p>
                                    <p class="text-xs text-slate-400">{{ $payment->created_at->format('d/m/Y H:i') }} · {{ ucfirst($payment->method) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Columna derecha: resumen --}}
        <div class="space-y-4">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
                <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Proveedor</p>
                @if($purchase->supplier)
                    <a href="{{ route('store.suppliers.show', $purchase->supplier) }}" class="font-semibold text-indigo-600 hover:underline">
                        {{ $purchase->supplier->name }}
                    </a>
                @else
                    <p class="font-semibold text-slate-800">Proveedor general</p>
                @endif
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-slate-500">Tipo</span>
                    <span class="text-xs px-2.5 py-1 rounded-full font-medium {{ $purchase->type === 'credito' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-600' }}">
                        {{ $purchase->type === 'credito' ? 'Crédito' : 'Contado' }}
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-slate-500">Estado</span>
                    @if($purchase->status === 'pagada')
                        <span class="bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded-full text-xs font-medium">Pagada</span>
                    @elseif($purchase->status === 'parcial')
                        <span class="bg-amber-100 text-amber-700 px-2.5 py-1 rounded-full text-xs font-medium">Parcial</span>
                    @else
                        <span class="bg-red-100 text-red-700 px-2.5 py-1 rounded-full text-xs font-medium">Pendiente</span>
                    @endif
                </div>
                <div class="pt-3 border-t border-slate-100 flex justify-between items-center">
                    <span class="text-sm text-slate-500">Total</span>
                    <span class="font-bold text-slate-800">${{ number_format($purchase->total, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-slate-500">Pagado</span>
                    <span class="font-semibold text-emerald-600">${{ number_format($purchase->paid, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-slate-500">Pendiente</span>
                    <span class="font-bold {{ $purchase->debt > 0 ? 'text-red-500' : 'text-slate-300' }}">${{ number_format($purchase->debt, 0, ',', '.') }}</span>
                </div>
            </div>

            @if($purchase->debt > 0 && $purchase->supplier)
                <a href="{{ route('store.suppliers.show', $purchase->supplier) }}"
                   class="block text-center bg-emerald-600 text-white py-3 rounded-xl font-semibold hover:bg-emerald-700 transition-colors shadow-sm">
                    💵 Ir a registrar abono
                </a>
            @endif

            <a href="{{ route('store.purchases.index') }}" class="block text-center text-slate-500 text-sm py-2 hover:underline">
                ← Volver a compras
            </a>
        </div>
    </div>

@endsection