<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
    'image_path',
    'alt_text',
    'image_class',
    'sort_order',
    'is_featured',
    'is_active',
])]
class LineupArtist extends Model
{
    protected static function booted(): void
    {
        static::saving(function (self $artist): void {
            $artist->alt_text = filled($artist->name) ? $artist->name : 'Lineup artist';
        });
    }

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
