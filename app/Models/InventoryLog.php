<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class InventoryLog extends Model
{
    protected $fillable = ['store_id', 'product_id', 'store_user_id', 'type', 'quantity', 'stock_before', 'stock_after', 'note'];

    public function store() {
        return $this->belongsTo(Store::class);
    }
    public function product() {
        return $this->belongsTo(Product::class);
    }
    public function storeUser() {
        return $this->belongsTo(StoreUser::class);
    }
}