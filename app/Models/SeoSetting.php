<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'site_name',
    'meta_title',
    'meta_description',
    'meta_keywords',
    'canonical_url',
    'robots_index',
    'robots_follow',
    'og_title',
    'og_description',
    'og_image_path',
    'og_type',
    'twitter_card',
    'twitter_title',
    'twitter_description',
    'twitter_image_path',
    'theme_color',
    'locale',
    'schema_json',
    'google_site_verification',
    'bing_site_verification',
    'is_active',
])]
class SeoSetting extends Model
{
    protected function casts(): array
    {
        return [
            'robots_index' => 'boolean',
            'robots_follow' => 'boolean',
            'schema_json' => 'array',
            'is_active' => 'boolean',
        ];
    }
}
