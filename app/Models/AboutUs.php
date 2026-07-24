<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'label',
    'organization_kicker',
    'organization_title',
    'organization_body',
    'history_kicker',
    'history_title',
    'history_body',
    'history_cta_label',
    'history_cta_url',
    'is_active',
])]
class AboutUs extends Model
{
    protected $table = 'about_us';

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
}
