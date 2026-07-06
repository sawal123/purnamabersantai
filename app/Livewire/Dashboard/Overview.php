<?php

namespace App\Livewire\Dashboard;

use App\Support\DashboardResourceRegistry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts::app')]
class Overview extends Component
{
    public function render()
    {
        $resources = collect(DashboardResourceRegistry::all())
            ->reject(fn (array $config) => (bool) ($config['hidden'] ?? false))
            ->map(fn (array $config, string $key) => $this->resourceSummary($key, $config))
            ->values();

        $moduleCount = $resources->count();
        $activeModuleCount = $resources->filter(fn (array $resource) => $resource['active_count'] > 0)->count();
        $recordCount = $resources->sum('count');
        $activeRecordCount = $resources->sum('active_count');
        $inactiveRecordCount = max(0, $recordCount - $activeRecordCount);
        $contentReadiness = $moduleCount > 0 ? (int) round(($activeModuleCount / $moduleCount) * 100) : 0;
        $lastUpdatedAt = $resources
            ->pluck('latest_updated_at')
            ->filter()
            ->sortDesc()
            ->first();

        return view('dashboard', [
            'resources' => $resources,
            'moduleCount' => $moduleCount,
            'activeModuleCount' => $activeModuleCount,
            'recordCount' => $recordCount,
            'activeRecordCount' => $activeRecordCount,
            'inactiveRecordCount' => $inactiveRecordCount,
            'groupCount' => $resources->pluck('navigation_group')->unique()->count(),
            'contentReadiness' => $contentReadiness,
            'lastUpdatedAt' => $lastUpdatedAt,
            'groups' => $this->groupSummaries($resources),
            'healthChecks' => $this->healthChecks($resources),
            'needsAttention' => $this->needsAttention($resources),
            'recentUpdates' => $this->recentUpdates($resources),
            'quickActions' => $this->quickActions($resources),
        ]);
    }

    protected function resourceSummary(string $key, array $config): array
    {
        $model = $config['model'];
        $instance = new $model;
        $table = $instance->getTable();
        $exists = Schema::hasTable($table);
        $hasActiveColumn = $exists && Schema::hasColumn($table, 'is_active');
        $hasUpdatedAtColumn = $exists && Schema::hasColumn($table, 'updated_at');
        $count = $exists ? $model::query()->count() : 0;
        $activeCount = $hasActiveColumn ? $model::query()->where('is_active', true)->count() : $count;
        $latestUpdatedAt = $hasUpdatedAtColumn ? $model::query()->max('updated_at') : null;

        return [
            'key' => $key,
            'label' => $config['label'],
            'description' => $config['description'],
            'navigation_group' => $config['navigation_group'],
            'icon' => $config['navigation_icon'] ?? 'rectangle-stack',
            'count' => $count,
            'active_count' => $activeCount,
            'inactive_count' => max(0, $count - $activeCount),
            'completion' => $count > 0 ? (int) round(($activeCount / $count) * 100) : 0,
            'exists' => $exists,
            'latest_updated_at' => $latestUpdatedAt,
        ];
    }

    protected function groupSummaries($resources)
    {
        return $resources
            ->groupBy('navigation_group')
            ->map(function ($items, string $heading) {
                $count = $items->sum('count');
                $activeCount = $items->sum('active_count');

                return [
                    'heading' => $heading,
                    'module_count' => $items->count(),
                    'count' => $count,
                    'active_count' => $activeCount,
                    'completion' => $count > 0 ? (int) round(($activeCount / $count) * 100) : 0,
                ];
            })
            ->values();
    }

    protected function healthChecks($resources)
    {
        return collect([
            ['key' => 'seo-setting', 'title' => 'SEO landing siap', 'description' => 'Meta tag, sharing preview, dan robots sudah punya data aktif.'],
            ['key' => 'landing-setting', 'title' => 'Identitas landing aktif', 'description' => 'Nama situs, hero copy, footer, dan info event sudah terisi.'],
            ['key' => 'hero-image', 'title' => 'Hero punya visual', 'description' => 'Landing page punya minimal satu gambar hero aktif.'],
            ['key' => 'ticket', 'title' => 'Ticketing tampil', 'description' => 'Pengunjung bisa melihat batch tiket aktif.'],
            ['key' => 'lineup-artist', 'title' => 'Lineup terisi', 'description' => 'Artis aktif sudah siap tampil di landing page.'],
            ['key' => 'contact-channel', 'title' => 'Kontak tersedia', 'description' => 'Kanal kontak aktif sudah bisa diakses pengunjung.'],
        ])->map(function (array $check) use ($resources) {
            $resource = $resources->firstWhere('key', $check['key']);
            $activeCount = (int) ($resource['active_count'] ?? 0);

            return [
                ...$check,
                'active_count' => $activeCount,
                'is_complete' => $activeCount > 0,
                'url' => $resource ? route('dashboard.resource', $resource['key']) : null,
            ];
        });
    }

    protected function needsAttention($resources)
    {
        return $resources
            ->filter(fn (array $resource) => $resource['count'] === 0 || $resource['active_count'] === 0)
            ->map(fn (array $resource) => [
                ...$resource,
                'reason' => $resource['count'] === 0 ? 'Belum ada record' : 'Tidak ada record aktif',
            ])
            ->take(5)
            ->values();
    }

    protected function recentUpdates($resources)
    {
        return $resources
            ->map(function (array $resource) {
                if (! $resource['exists'] || $resource['latest_updated_at'] === null) {
                    return null;
                }

                $config = DashboardResourceRegistry::get($resource['key']);
                $model = $config['model'];
                $record = $model::query()->latest('updated_at')->first();

                if (! $record instanceof Model) {
                    return null;
                }

                return [
                    'resource_key' => $resource['key'],
                    'resource_label' => $resource['label'],
                    'record_label' => $this->recordLabel($record),
                    'updated_at' => $record->getAttribute('updated_at'),
                    'url' => route('dashboard.resource', $resource['key']),
                ];
            })
            ->filter()
            ->sortByDesc('updated_at')
            ->take(6)
            ->values();
    }

    protected function quickActions($resources)
    {
        return collect(['seo-setting', 'landing-setting', 'hero-image', 'ticket', 'lineup-artist', 'gallery-moment'])
            ->map(fn (string $key) => $resources->firstWhere('key', $key))
            ->filter()
            ->map(fn (array $resource) => [
                'key' => $resource['key'],
                'label' => $resource['label'],
                'description' => $this->quickActionDescription($resource['key']),
                'icon' => $resource['icon'],
                'url' => route('dashboard.resource', $resource['key']),
            ])
            ->values();
    }

    protected function quickActionDescription(string $key): string
    {
        return match ($key) {
            'seo-setting' => 'Atur title, description, OG image',
            'landing-setting' => 'Edit identitas dan hero copy',
            'hero-image' => 'Tambah visual utama landing',
            'ticket' => 'Kelola batch dan link tiket',
            'lineup-artist' => 'Update artis yang tampil',
            'gallery-moment' => 'Kurasi momen festival',
            default => 'Kelola konten',
        };
    }

    protected function recordLabel(Model $record): string
    {
        foreach (['title', 'name', 'question', 'site_name', 'meta_title', 'label', 'batch_label', 'alt_text', 'image_path'] as $column) {
            $value = $record->getAttribute($column);

            if (is_scalar($value) && filled($value)) {
                return str($value)->limit(64)->toString();
            }
        }

        return '#'.$record->getKey();
    }
}
