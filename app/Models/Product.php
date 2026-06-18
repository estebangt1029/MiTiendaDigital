<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['store_id', 'category_id', 'name', 'barcode', 'price', 'cost', 'stock', 'min_stock', 'active'];

    public function store() {
        return $this->belongsTo(Store::class);
    }
    public function category() {
        return $this->belongsTo(Category::class);
    }
    public function saleItems() {
        return $this->hasMany(SaleItem::class);
    }
    public function inventoryLogs() {
        return $this->hasMany(InventoryLog::class);
    }

    public function isLowStock(): bool {
        return $this->stock <= $this->min_stock;
    }
}