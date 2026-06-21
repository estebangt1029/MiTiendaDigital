<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SupplierPayment extends Model
{
    protected $fillable = [
        'store_id', 'supplier_id', 'purchase_id', 'store_user_id',
        'amount', 'method', 'notes',
    ];

    public function store() {
        return $this->belongsTo(Store::class);
    }

    public function supplier() {
        return $this->belongsTo(Supplier::class);
    }

    public function purchase() {
        return $this->belongsTo(Purchase::class);
    }

    public function storeUser() {
        return $this->belongsTo(StoreUser::class);
    }
}