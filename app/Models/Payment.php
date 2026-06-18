<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['sale_id', 'customer_id', 'store_id', 'store_user_id', 'amount', 'method', 'notes'];

    public function sale() {
        return $this->belongsTo(Sale::class);
    }
    public function customer() {
        return $this->belongsTo(Customer::class);
    }
    public function store() {
        return $this->belongsTo(Store::class);
    }
    public function storeUser() {
        return $this->belongsTo(StoreUser::class);
    }
}