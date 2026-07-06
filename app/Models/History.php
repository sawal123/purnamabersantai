<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'order_by',
    'title',
    'tahun',
    'lokasi',
    'capacity',
    'tanggal_acara',
    'content',
    'thumbnail',
    'media',
    'festival_galery',
    'is_active',
])]
class History extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'order_by' => 'integer',
            'tahun' => 'integer',
            'capacity' => 'integer',
            'tanggal_acara' => 'date',
            'media' => 'array',
            'festival_galery' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order_by')->orderByDesc('tahun')->orderBy('id');
    }
}
