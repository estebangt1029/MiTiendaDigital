<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'store_id', 'name', 'contact_name', 'phone', 'email',
        'address', 'notes', 'total_debt', 'active',
    ];

    public function store() {
        return $this->belongsTo(Store::class);
    }

    public function purchases() {
        return $this->hasMany(Purchase::class);
    }

    public function payments() {
        return $this->hasMany(SupplierPayment::class);
    }
}