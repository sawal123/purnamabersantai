<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'merchandise_product_id',
    'image_path',
    'alt_text',
    'image_class',
    'sort_order',
    'is_active',
])]
class MerchandiseProductImage extends Model
{
    protected static function booted(): void
    {
        static::saving(function (self $image): void {
            $image->alt_text = $image->automaticAltText();
        });
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(MerchandiseProduct::class, 'merchandise_product_id');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    protected function automaticAltText(): string
    {
        $productName = $this->product?->name;

        if (blank($productName) && filled($this->merchandise_product_id)) {
            $productName = MerchandiseProduct::query()
                ->whereKey($this->merchandise_product_id)
                ->value('name');
        }

        return filled($productName)
            ? $productName
            : 'Merchandise product image';
    }
}
