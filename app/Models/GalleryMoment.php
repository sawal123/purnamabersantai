<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'title',
    'username',
    'description',
    'image_path',
    'alt_text',
    'sort_order',
    'is_active',
])]
class GalleryMoment extends Model
{
    protected static function booted(): void
    {
        static::saving(function (self $moment): void {
            if (blank($moment->alt_text)) {
                $moment->alt_text = filled($moment->title) ? $moment->title : 'Gallery moment';
            }
        });
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
