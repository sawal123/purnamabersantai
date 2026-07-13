<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'title',
    'artist',
    'audio_path',
    'duration_label',
    'sort_order',
    'is_active',
])]
class Song extends Model
{
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function getAudioUrlAttribute(): string
    {
        return str_starts_with($this->audio_path, 'http') || str_starts_with($this->audio_path, '/')
            ? $this->audio_path
            : asset($this->audio_path);
    }

    public function getDisplayTitleAttribute(): string
    {
        return filled($this->artist)
            ? "{$this->artist} - {$this->title}"
            : $this->title;
    }
}
