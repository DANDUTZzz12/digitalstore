<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'terms_html',
        'short_description',
        'image',
        'price',
        'is_auto_send',
        'is_best_seller',
        'sold_count',
        'category_id',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'is_auto_send' => 'boolean',
            'is_best_seller' => 'boolean',
            'sold_count' => 'integer',
        ];
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function imageUrl(): ?string
    {
        if (! $this->image) {
            return null;
        }
        // Already a full URL?
        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }

        return Storage::disk('public')->url($this->image);
    }

    public function lowestPrice(): int
    {
        // Pakai relation collection kalau sudah di-eager-load (hindari N+1).
        // Fallback ke query DB hanya kalau variants belum dimuat.
        if ($this->relationLoaded('variants')) {
            return (int) ($this->variants->min('price') ?? $this->price);
        }

        return (int) ($this->variants()->min('price') ?? $this->price);
    }
}
