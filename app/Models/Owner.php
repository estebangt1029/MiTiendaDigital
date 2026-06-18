<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Owner extends Authenticatable
{
    protected $fillable = ['name', 'email', 'password', 'phone', 'active'];
    protected $hidden = ['password'];

    public function stores() {
        return $this->hasMany(Store::class);
    }
    public function subscriptions() {
        return $this->hasMany(Subscription::class);
    }
}