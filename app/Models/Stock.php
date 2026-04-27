<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    // Membuka gerbang agar form bisa menyimpan data
    protected $guarded = [];

    // INI DIA JEMBATAN YANG DICARI FILAMENT
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}