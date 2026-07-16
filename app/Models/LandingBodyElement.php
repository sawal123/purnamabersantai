<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'title',
    'page_section',
    'image_path',
    'is_active',
])]
class LandingBodyElement extends Model
{
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForSection(Builder $query, string $section): Builder
    {
        return $query->where('page_section', $section);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('id');
    }
}
