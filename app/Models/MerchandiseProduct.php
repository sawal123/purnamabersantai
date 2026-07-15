<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable([
    'merchandise_product_category_id',
    'slug',
    'kicker',
    'name',
    'price',
    'discount_price',
    'currency',
    'stock_quantity',
    'description',
    'size_options',
    'color_options',
    'thumbnail_path',
    'thumbnail_alt',
    'thumbnail_class',
    'order_url',
    'sort_order',
    'is_active',
])]
class MerchandiseProduct extends Model
{
    protected static function booted(): void
    {
        static::saving(function (self $product): void {
            $product->thumbnail_alt = $product->automaticThumbnailAlt();
        });
    }

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'discount_price' => 'integer',
            'stock_quantity' => 'integer',
            'size_options' => 'array',
            'color_options' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(MerchandiseProductCategory::class, 'merchandise_product_category_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(MerchandiseProductImage::class)->ordered();
    }

    public function features(): HasMany
    {
        return $this->hasMany(MerchandiseProductFeature::class)->ordered();
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function hasDiscount(): bool
    {
        return (int) $this->price > 0
            && (int) $this->discount_price > 0
            && (int) $this->discount_price < (int) $this->price;
    }

    public function effectivePrice(): int
    {
        return $this->hasDiscount() ? (int) $this->discount_price : (int) $this->price;
    }

    public function discountPercent(): ?int
    {
        if (! $this->hasDiscount()) {
            return null;
        }

        return (int) round((((int) $this->price - (int) $this->discount_price) / (int) $this->price) * 100);
    }

    protected function automaticThumbnailAlt(): string
    {
        if (filled($this->name)) {
            return $this->name;
        }

        if (filled($this->slug)) {
            return Str::title(str_replace('-', ' ', $this->slug));
        }

        return 'Merchandise product';
    }
}
