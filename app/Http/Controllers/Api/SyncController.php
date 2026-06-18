<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\Product;
use App\Models\Customer;
use App\Models\InventoryLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SyncController extends Controller
{
    public function syncSale(Request $request)
    {
        $request->validate([
            'store_id'    => 'required|exists:stores,id',
            'type'        => 'required|in:contado,fiado',
            'items'       => 'required|array|min:1',
            'paid'        => 'required|numeric|min:0',
            'customer_id' => 'nullable|exists:customers,id',
        ]);

        $store = Store::findOrFail($request->store_id);

        DB::transaction(function () use ($request, $store) {
            $total     = 0;
            $itemsData = [];

            foreach ($request->items as $item) {
                $product  = Product::where('store_id', $store->id)->findOrFail($item['product_id']);
                $subtotal = $product->price * $item['quantity'];
                $total   += $subtotal;
                $itemsData[] = ['product' => $product, 'quantity' => $item['quantity'], 'price' => $product->price, 'subtotal' => $subtotal];
            }

            $paid   = min($request->paid, $total);
            $debt   = $total - $paid;
            $status = $debt <= 0 ? 'pagada' : ($paid > 0 ? 'parcial' : 'pendiente');

            $sale = $store->sales()->create([
                'customer_id'   => $request->customer_id,
                'store_user_id' => $request->store_user_id ?? null,
                'type'          => $request->type,
                'total'         => $total,
                'paid'          => $paid,
                'debt'          => $debt,
                'status'        => $status,
                'notes'         => $request->notes ?? null,
            ]);

            foreach ($itemsData as $item) {
                $sale->items()->create([
                    'product_id' => $item['product']->id,
                    'quantity'   => $item['quantity'],
                    'unit_price' => $item['price'],
                    'subtotal'   => $item['subtotal'],
                ]);
                $before = $item['product']->stock;
                $item['product']->decrement('stock', $item['quantity']);
                InventoryLog::create([
                    'store_id'    => $store->id,
                    'product_id'  => $item['product']->id,
                    'type'        => 'venta',
                    'quantity'    => $item['quantity'],
                    'stock_before'=> $before,
                    'stock_after' => $before - $item['quantity'],
                    'note'        => "Venta offline #{$sale->id}",
                ]);
            }

            if ($request->customer_id && $debt > 0) {
                Customer::find($request->customer_id)->increment('total_debt', $debt);
            }
        });

        return response()->json(['success' => true]);
    }
}