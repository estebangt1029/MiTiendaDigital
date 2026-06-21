<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
        'store_id', 'supplier_id', 'store_user_id', 'type',
        'total', 'paid', 'debt', 'status', 'update_product_cost', 'notes',
    ];

    public function store() {
        return $this->belongsTo(Store::class);
    }

    public function supplier() {
        return $this->belongsTo(Supplier::class);
    }

    public function storeUser() {
        return $this->belongsTo(StoreUser::class);
    }

    public function items() {
        return $this->hasMany(PurchaseItem::class);
    }

    public function payments() {
        return $this->hasMany(SupplierPayment::class);
    }
}