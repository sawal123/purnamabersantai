<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'site_name',
    'page_title',
    'hero_tagline',
    'hero_description',
    'logo_path',
    'hero_brand_path',
    'video_url',
    'footer_description',
    'sponsor_text',
    'event_info',
    'is_active',
])]
class LandingSetting extends Model
{
    protected function casts(): array
    {
        return [
            'event_info' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function heroImages(): HasMany
    {
        return $this->hasMany(LandingHeroImage::class)->ordered();
    }
}
