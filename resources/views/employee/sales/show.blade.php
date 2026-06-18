@extends('layouts.storeuser')
@section('title', 'Venta #' . $sale->id)
@section('page-title', 'Venta #' . $sale->id)
@section('page-subtitle', 'Detalle de la venta')

@section('content')
    <div class="space-y-6 max-w-3xl">

        <div class="bg-white rounded-xl shadow p-5 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div>
                <p class="text-xs text-gray-400 uppercase">Fecha</p>
                <p class="font-medium mt-1">{{ $sale->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase">Tipo</p>
                <p class="font-medium capitalize mt-1">{{ $sale->type }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase">Cliente</p>
                <p class="font-medium mt-1">{{ $sale->customer->name ?? 'General' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase">Estado</p>
                <div class="mt-1">
                    @if($sale->status === 'pagada')
                        <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-xs">Pagada</span>
                    @elseif($sale->status === 'parcial')
                        <span class="bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full text-xs">Parcial</span>
                    @else
                        <span class="bg-red-100 text-red-700 px-2 py-0.5 rounded-full text-xs">Pendiente</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="px-5 py-4 border-b font-semibold text-gray-700">Productos</div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left">Producto</th>
                        <th class="px-4 py-3 text-right">Precio</th>
                        <th class="px-4 py-3 text-right">Cantidad</th>
                        <th class="px-4 py-3 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($sale->items as $item)
                        <tr>
                            <td class="px-4 py-3">{{ $item->product->name }}</td>
                            <td class="px-4 py-3 text-right">${{ number_format($item->unit_price, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right">{{ $item->quantity }}</td>
                            <td class="px-4 py-3 text-right font-medium">${{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 text-sm">
                    <tr>
                        <td colspan="3" class="px-4 py-3 text-right font-semibold">Total</td>
                        <td class="px-4 py-3 text-right font-bold text-green-600">${{ number_format($sale->total, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="px-4 py-2 text-right text-gray-500">Pagado</td>
                        <td class="px-4 py-2 text-right text-green-600">${{ number_format($sale->paid, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="px-4 py-2 text-right text-gray-500">Deuda</td>
                        <td class="px-4 py-2 text-right {{ $sale->debt > 0 ? 'text-red-600 font-bold' : 'text-gray-400' }}">
                            ${{ number_format($sale->debt, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        @if($sale->debt > 0)
            <div class="bg-white rounded-xl shadow p-5">
                <h3 class="font-semibold mb-4 text-gray-700">Registrar abono</h3>
                <form method="POST" action="{{ route('employee.customers.pay', $sale->customer) }}"
                      class="flex gap-3 flex-wrap">
                    @csrf
                    <input type="hidden" name="sale_id" value="{{ $sale->id }}">
                    <div class="flex-1 min-w-32">
                        <label class="text-xs text-gray-500 mb-1 block">Monto (máx ${{ number_format($sale->debt, 0, ',', '.') }})</label>
                        <input type="number" name="amount" min="1" max="{{ $sale->debt }}" required
                            class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    <div class="w-40">
                        <label class="text-xs text-gray-500 mb-1 block">Método</label>
                        <select name="method" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none">
                            <option value="efectivo">Efectivo</option>
                            <option value="transferencia">Transferencia</option>
                            <option value="nequi">Nequi</option>
                            <option value="daviplata">Daviplata</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit"
                            class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 font-medium">
                            Registrar abono
                        </button>
                    </div>
                </form>
            </div>
        @endif

        @if($sale->payments->count() > 0)
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="px-5 py-4 border-b font-semibold text-gray-700">Historial de abonos</div>
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3 text-left">Fecha</th>
                            <th class="px-4 py-3 text-left">Método</th>
                            <th class="px-4 py-3 text-right">Monto</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($sale->payments as $payment)
                            <tr>
                                <td class="px-4 py-3 text-gray-500">{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3 capitalize">{{ $payment->method }}</td>
                                <td class="px-4 py-3 text-right text-green-600 font-medium">
                                    ${{ number_format($payment->amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <div>
            <a href="{{ route('employee.sales.index') }}" class="text-gray-400 hover:text-gray-600 text-sm">← Volver a ventas</a>
        </div>
    </div>
@endsection