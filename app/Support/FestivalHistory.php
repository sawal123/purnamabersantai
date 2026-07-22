<?php

namespace App\Support;

use App\Models\History as HistoryModel;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class FestivalHistory
{
    public static function all(): array
    {
        if (Schema::hasTable('histories')) {
            $histories = HistoryModel::query()
                ->where('is_active', true)
                ->ordered()
                ->get();

            return $histories
                ->map(fn (HistoryModel $history) => static::fromModel($history))
                ->all();
        }

        return [];
    }

    public static function latest(int $limit = 3): array
    {
        return array_slice(static::all(), 0, $limit);
    }

    public static function findByTitle(string $title): ?array
    {
        $lookup = Str::slug($title);
        $plainTitle = Str::lower($title);

        foreach (static::all() as $history) {
            if (($history['slug'] ?? '') === $lookup || Str::lower($history['title']) === $plainTitle) {
                return $history;
            }
        }

        return null;
    }

    protected static function fromModel(HistoryModel $history): array
    {
        $plainContent = trim(strip_tags((string) $history->content));

        return [
            'year' => (string) $history->tahun,
            'title' => $history->title,
            'slug' => Str::slug($history->title),
            'location' => $history->lokasi,
            'capacity' => $history->capacity,
            'capacity_label' => $history->capacity ? number_format($history->capacity) : null,
            'date' => $history->tanggal_acara?->toDateString(),
            'date_label' => $history->tanggal_acara?->format('d M Y'),
            'summary' => Str::limit($plainContent, 180),
            'content' => $history->content,
            'thumbnail' => $history->thumbnail,
            'media' => array_values($history->media ?? []),
            'gallery' => array_values($history->festival_galery ?? []),
        ];
    }
}
