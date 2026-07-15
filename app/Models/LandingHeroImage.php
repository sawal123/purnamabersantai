<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'landing_setting_id',
    'image_path',
    'alt_text',
    'sort_order',
    'is_active',
])]
class LandingHeroImage extends Model
{
    protected static function booted(): void
    {
        static::saving(function (self $heroImage): void {
            $heroImage->alt_text = $heroImage->automaticAltText();
        });
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function landingSetting(): BelongsTo
    {
        return $this->belongsTo(LandingSetting::class);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    protected function automaticAltText(): string
    {
        $siteName = $this->landingSetting?->site_name;

        if (blank($siteName) && filled($this->landing_setting_id)) {
            $siteName = LandingSetting::query()
                ->whereKey($this->landing_setting_id)
                ->value('site_name');
        }

        return filled($siteName)
            ? "{$siteName} hero image"
            : 'Purnama Bersantai hero image';
    }
}
