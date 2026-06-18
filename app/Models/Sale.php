<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = ['store_id', 'customer_id', 'store_user_id', 'type', 'total', 'paid', 'debt', 'status', 'notes'];

    public function store() {
        return $this->belongsTo(Store::class);
    }
    public function customer() {
        return $this->belongsTo(Customer::class);
    }
    public function storeUser() {
        return $this->belongsTo(StoreUser::class);
    }
    public function items() {
        return $this->hasMany(SaleItem::class);
    }
    public function payments() {
        return $this->hasMany(Payment::class);
    }
}