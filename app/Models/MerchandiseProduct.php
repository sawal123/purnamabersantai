<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'slug',
    'kicker',
    'name',
    'price',
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
    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'stock_quantity' => 'integer',
            'size_options' => 'array',
            'color_options' => 'array',
            'is_active' => 'boolean',
        ];
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
}
