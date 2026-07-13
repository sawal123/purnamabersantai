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

            if ($histories->isNotEmpty()) {
                return $histories
                    ->map(fn (HistoryModel $history) => static::fromModel($history))
                    ->all();
            }
        }

        return [
            [
                'year' => '2025',
                'title' => 'Purnama Bersantai: Moonlit Weekend',
                'slug' => 'purnama-bersantai-moonlit-weekend',
                'location' => 'Jakarta, Indonesia',
                'capacity' => 7200,
                'capacity_label' => '7,200',
                'date' => '2025-08-09',
                'date_label' => '09 Aug 2025',
                'summary' => 'Memperluas pengalaman festival dengan panggung yang lebih intim, aktivasi komunitas, dan area merchandise yang lebih hidup.',
                'content' => 'Memperluas pengalaman festival dengan panggung yang lebih intim, aktivasi komunitas, dan area merchandise yang lebih hidup. Edisi ini mempertemukan penonton, musisi lokal, dan tenant pilihan dalam suasana malam yang lebih dekat.',
                'thumbnail' => 'landing/assets/hero/image 1.png',
                'media' => [
                    'landing/assets/hero/image 1.png',
                ],
                'gallery' => [
                    'landing/assets/Rectangle 17.png',
                    'landing/assets/Rectangle 15.png',
                    'landing/assets/image 8.png',
                ],
            ],
            [
                'year' => '2024',
                'title' => 'Purnama Bersantai: City Lights Session',
                'slug' => 'purnama-bersantai-city-lights-session',
                'location' => 'Jakarta, Indonesia',
                'capacity' => 5200,
                'capacity_label' => '5,200',
                'date' => '2024-08-10',
                'date_label' => '10 Aug 2024',
                'summary' => 'Mulai membangun identitas festival malam yang santai dengan lineup lokal, tenant pilihan, dan atmosfer tropis urban.',
                'content' => 'Mulai membangun identitas festival malam yang santai dengan lineup lokal, tenant pilihan, dan atmosfer tropis urban. City Lights Session menjadi ruang temu yang lebih rapi untuk komunitas musik dan kreator muda.',
                'thumbnail' => 'landing/assets/hero/2022_03_29_124082_1648520536._large.jpg',
                'media' => [
                    'landing/assets/hero/2022_03_29_124082_1648520536._large.jpg',
                ],
                'gallery' => [
                    'landing/assets/Rectangle 17.png',
                    'landing/assets/Group 30.png',
                    'landing/assets/3.png',
                ],
            ],
            [
                'year' => '2023',
                'title' => 'Purnama Bersantai: First Gathering',
                'slug' => 'purnama-bersantai-first-gathering',
                'location' => 'Jakarta, Indonesia',
                'capacity' => 4200,
                'capacity_label' => '4,200',
                'date' => '2023-08-12',
                'date_label' => '12 Aug 2023',
                'summary' => 'Menjadi titik awal pertemuan komunitas musik, kreator lokal, dan penonton yang mencari pengalaman event yang lebih personal.',
                'content' => 'Menjadi titik awal pertemuan komunitas musik, kreator lokal, dan penonton yang mencari pengalaman event yang lebih personal. Dari format ini, Purnama Bersantai mulai menemukan karakter hangat dan komunalnya.',
                'thumbnail' => 'landing/assets/Rectangle 17.png',
                'media' => [
                    'landing/assets/Rectangle 17.png',
                ],
                'gallery' => [
                    'landing/assets/Rectangle 15.png',
                    'landing/assets/image 8.png',
                    'landing/assets/3-cropped.png',
                ],
            ],
        ];
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
