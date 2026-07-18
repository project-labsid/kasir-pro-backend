<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku', 'name', 'category_id', 'supplier_id', 
        'purchase_price', 'selling_price', 'stock', 
        'min_stock', 'unit', 'image'
    ];

    // Relasi ke tabel categories
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relasi ke tabel suppliers
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}