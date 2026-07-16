<?php

namespace App\Http\Controllers;

use App\Models\History;
use App\Models\MerchandiseProduct;
use App\Support\FestivalHistory;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Throwable;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = collect([
            $this->url(route('home'), 'weekly', '1.0'),
            $this->url(route('landing.lineup'), 'weekly', '0.8'),
            $this->url(route('landing.tickets'), 'weekly', '0.9'),
            $this->url(route('landing.merch'), 'weekly', '0.8'),
            $this->url(route('landing.gallery'), 'monthly', '0.7'),
            $this->url(route('landing.playlist'), 'monthly', '0.6'),
            $this->url(route('landing.rundown-map'), 'weekly', '0.7'),
            $this->url(route('landing.about'), 'monthly', '0.7'),
            $this->url(route('landing.history'), 'monthly', '0.7'),
            $this->url(route('landing.sponsors'), 'monthly', '0.6'),
            $this->url(route('landing.contact'), 'monthly', '0.6'),
            $this->url(route('landing.faq'), 'monthly', '0.6'),
        ])
            ->merge($this->merchandiseUrls())
            ->merge($this->historyUrls())
            ->unique('loc')
            ->values();

        return response($this->toXml($urls->all()), 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    private function merchandiseUrls(): array
    {
        if (! $this->hasTable('merchandise_products')) {
            return [];
        }

        try {
            return MerchandiseProduct::query()
                ->where('is_active', true)
                ->whereNotNull('slug')
                ->ordered()
                ->get(['slug', 'updated_at'])
                ->map(fn (MerchandiseProduct $product) => $this->url(
                    route('landing.merch.show', ['productSlug' => $product->slug]),
                    'weekly',
                    '0.7',
                    $product->updated_at,
                ))
                ->all();
        } catch (Throwable) {
            return [];
        }
    }

    private function historyUrls(): array
    {
        if ($this->hasTable('histories')) {
            try {
                if (History::query()->where('is_active', true)->exists()) {
                    return History::query()
                        ->where('is_active', true)
                        ->ordered()
                        ->get(['title', 'updated_at'])
                        ->map(fn (History $history) => $this->url(
                            route('landing.history.show', ['title' => str($history->title)->slug()->toString()]),
                            'monthly',
                            '0.7',
                            $history->updated_at,
                        ))
                        ->all();
                }
            } catch (Throwable) {
                return $this->fallbackHistoryUrls();
            }
        }

        return $this->fallbackHistoryUrls();
    }

    private function fallbackHistoryUrls(): array
    {
        try {
            return collect(FestivalHistory::all())
                ->map(fn (array $history) => $this->url(
                    route('landing.history.show', ['title' => $history['slug']]),
                    'monthly',
                    '0.7',
                    $history['date'] ?? null,
                ))
                ->all();
        } catch (Throwable) {
            return [];
        }
    }

    private function hasTable(string $table): bool
    {
        try {
            return Schema::hasTable($table);
        } catch (Throwable) {
            return false;
        }
    }

    private function url(string $loc, string $changefreq, string $priority, Carbon|string|null $lastmod = null): array
    {
        return [
            'loc' => $loc,
            'lastmod' => $lastmod instanceof Carbon ? $lastmod->toDateString() : $lastmod,
            'changefreq' => $changefreq,
            'priority' => $priority,
        ];
    }

    /**
     * @param  array<int, array{loc: string, lastmod: string|null, changefreq: string, priority: string}>  $urls
     */
    private function toXml(array $urls): string
    {
        $xml = ['<?xml version="1.0" encoding="UTF-8"?>'];
        $xml[] = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($urls as $url) {
            $xml[] = '    <url>';
            $xml[] = '        <loc>'.$this->escape($url['loc']).'</loc>';

            if (filled($url['lastmod'])) {
                $xml[] = '        <lastmod>'.$this->escape($url['lastmod']).'</lastmod>';
            }

            $xml[] = '        <changefreq>'.$this->escape($url['changefreq']).'</changefreq>';
            $xml[] = '        <priority>'.$this->escape($url['priority']).'</priority>';
            $xml[] = '    </url>';
        }

        $xml[] = '</urlset>';

        return implode(PHP_EOL, $xml).PHP_EOL;
    }

    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_COMPAT, 'UTF-8');
    }
}
