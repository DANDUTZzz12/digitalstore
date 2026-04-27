<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'name',
        'price',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    public function availableStocks(): HasMany
    {
        return $this->hasMany(Stock::class)->where('is_sold', false);
    }

    public function flashsales(): HasMany
    {
        return $this->hasMany(Flashsale::class);
    }

    public function activeFlashsale(): ?Flashsale
    {
        return Flashsale::active()->where('product_variant_id', $this->id)->first();
    }

    /** Harga efektif: pakai flashsale price kalau ada, jika tidak harga normal. */
    public function effectivePrice(): int
    {
        $fs = $this->activeFlashsale();

        return $fs ? (int) $fs->flash_price : (int) $this->price;
    }
}
