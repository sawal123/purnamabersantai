<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'title',
    'youtube_url',
    'aria_label',
    'sort_order',
    'is_active',
])]
class YoutubeVideo extends Model
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

    public function getYoutubeVideoIdAttribute(): ?string
    {
        return self::extractYoutubeVideoId($this->youtube_url);
    }

    public function getEmbedUrlAttribute(): ?string
    {
        return $this->youtube_video_id
            ? 'https://www.youtube.com/embed/'.$this->youtube_video_id
            : null;
    }

    public function getEmbedSrcAttribute(): ?string
    {
        return $this->embed_url
            ? $this->embed_url.'?rel=0&modestbranding=1'
            : null;
    }

    public static function extractYoutubeVideoId(?string $url): ?string
    {
        $url = trim((string) $url);

        if ($url === '') {
            return null;
        }

        if (preg_match('/^[A-Za-z0-9_-]{11}$/', $url) === 1) {
            return $url;
        }

        $parts = parse_url($url);

        if (! is_array($parts)) {
            return null;
        }

        parse_str($parts['query'] ?? '', $query);

        if (isset($query['v']) && preg_match('/^[A-Za-z0-9_-]{11}$/', (string) $query['v']) === 1) {
            return (string) $query['v'];
        }

        $path = trim((string) ($parts['path'] ?? ''), '/');
        $segments = array_values(array_filter(explode('/', $path)));

        if ($segments === []) {
            return null;
        }

        if (in_array($segments[0], ['embed', 'shorts', 'live'], true) && isset($segments[1])) {
            return preg_match('/^[A-Za-z0-9_-]{11}$/', $segments[1]) === 1 ? $segments[1] : null;
        }

        return preg_match('/^[A-Za-z0-9_-]{11}$/', $segments[0]) === 1 ? $segments[0] : null;
    }
}
