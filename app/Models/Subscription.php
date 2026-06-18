<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Subscription extends Model
{
    protected $fillable = [
        'owner_id', 'store_id', 'plan', 'price',
        'start_date', 'end_date', 'active',
        'status', 'payment_proof', 'notes', 'confirmed_at',
    ];

    protected $casts = [
        'start_date'   => 'date',
        'end_date'     => 'date',
        'confirmed_at' => 'datetime',
    ];

    public function owner() { return $this->belongsTo(Owner::class); }
    public function store() { return $this->belongsTo(Store::class); }

    public function isPending()  { return $this->status === 'pending'; }
    public function isActive()   { return $this->status === 'active' && $this->end_date->isFuture(); }
    public function isExpired()  { return $this->status === 'active' && $this->end_date->isPast(); }

    // Planes disponibles con precios y duración
    public static function plans(): array
    {
        return [
            '1_month'  => ['label' => '1 mes',   'months' => 1,  'price' => 105000],
            '3_months' => ['label' => '3 meses',  'months' => 3,  'price' => 300000],
            '6_months' => ['label' => '6 meses',  'months' => 6,  'price' => 600000],
            '1_year'   => ['label' => '1 año',    'months' => 12, 'price' => 1200000],
        ];
    }
}