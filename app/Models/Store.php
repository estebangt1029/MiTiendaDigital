<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $fillable = ['owner_id', 'name', 'address', 'phone', 'active'];

    public function owner() {
        return $this->belongsTo(Owner::class);
    }
    public function users() {
        return $this->hasMany(StoreUser::class);
    }
    public function categories() {
        return $this->hasMany(Category::class);
    }
    public function products() {
        return $this->hasMany(Product::class);
    }
    public function customers() {
        return $this->hasMany(Customer::class);
    }
    public function sales() {
        return $this->hasMany(Sale::class);
    }
    public function subscriptions() {
        return $this->hasMany(Subscription::class);
    }
    public function inventoryLogs() {
        return $this->hasMany(InventoryLog::class);
    }

    // ── Nuevo: proveedores y compras ──────────────────────
    public function suppliers() {
        return $this->hasMany(Supplier::class);
    }
    public function purchases() {
        return $this->hasMany(Purchase::class);
    }
    public function supplierPayments() {
        return $this->hasMany(SupplierPayment::class);
    }
}