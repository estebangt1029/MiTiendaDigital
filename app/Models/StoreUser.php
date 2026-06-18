<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;

class StoreUser extends Authenticatable
{
    protected $fillable = ['store_id', 'name', 'email', 'password', 'role', 'active'];
    protected $hidden = ['password'];

    public function store() {
        return $this->belongsTo(Store::class);
    }
    public function sales() {
        return $this->hasMany(Sale::class);
    }
    public function payments() {
        return $this->hasMany(Payment::class);
    }
    public function inventoryLogs() {
        return $this->hasMany(InventoryLog::class);
    }
}