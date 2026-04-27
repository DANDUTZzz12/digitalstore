<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Mematikan satpam dan mengizinkan semua kolom diisi
    protected $guarded = [];
    
    // --- TAMBAHKAN FUNGSI VARIAN DI SINI ---
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
    public function category()
{
    return $this->belongsTo(Category::class);
}
}