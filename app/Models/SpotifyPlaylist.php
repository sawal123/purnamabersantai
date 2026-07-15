<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'label',
    'title',
    'embed_url',
    'open_url',
    'sort_order',
    'is_active',
])]
class SpotifyPlaylist extends Model
{
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $playlist): void {
            $playlist->embed_url = self::normalizeEmbedUrl((string) $playlist->embed_url);
            $playlist->open_url = filled($playlist->open_url)
                ? self::normalizeOpenUrl((string) $playlist->open_url)
                : self::openUrlFromEmbed((string) $playlist->embed_url);
        });
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function resolvedOpenUrl(): string
    {
        return filled($this->open_url)
            ? $this->open_url
            : self::openUrlFromEmbed((string) $this->embed_url);
    }

    public static function normalizeEmbedUrl(string $value): string
    {
        $value = trim($value);

        if (preg_match('/src=["\']([^"\']+)["\']/i', $value, $matches) === 1) {
            $value = html_entity_decode($matches[1]);
        }

        $value = strtok($value, '#') ?: $value;

        if (str_contains($value, 'open.spotify.com/playlist/')) {
            $value = str_replace('open.spotify.com/playlist/', 'open.spotify.com/embed/playlist/', $value);
        }

        if (! str_contains($value, 'utm_source=')) {
            $separator = str_contains($value, '?') ? '&' : '?';
            $value .= $separator.'utm_source=generator';
        }

        if (! str_contains($value, 'theme=')) {
            $value .= '&theme=0';
        }

        return $value;
    }

    public static function normalizeOpenUrl(string $value): string
    {
        $value = trim($value);

        if (preg_match('/src=["\']([^"\']+)["\']/i', $value, $matches) === 1) {
            $value = html_entity_decode($matches[1]);
        }

        return str_replace('open.spotify.com/embed/playlist/', 'open.spotify.com/playlist/', $value);
    }

    public static function openUrlFromEmbed(string $value): string
    {
        $value = self::normalizeOpenUrl($value);
        $parts = parse_url($value);

        if (! is_array($parts) || empty($parts['scheme']) || empty($parts['host']) || empty($parts['path'])) {
            return $value;
        }

        return $parts['scheme'].'://'.$parts['host'].$parts['path'];
    }
}
