<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'placement',
    'label',
    'aria_label',
    'primary_text',
    'secondary_text',
    'repeat_count',
    'highlight_secondary',
    'is_active',
])]
class LandingMarquee extends Model
{
    protected function casts(): array
    {
        return [
            'repeat_count' => 'integer',
            'highlight_secondary' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('id');
    }
}
