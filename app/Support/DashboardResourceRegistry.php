<?php

namespace App\Support;

use InvalidArgumentException;

class DashboardResourceRegistry
{
    public static function all(): array
    {
        return config('dashboard-resources', []);
    }

    public static function keys(): array
    {
        return array_keys(self::all());
    }

    public static function get(string $resource): array
    {
        $config = self::all()[$resource] ?? null;

        if ($config === null) {
            throw new InvalidArgumentException("Unknown dashboard resource [{$resource}].");
        }

        return ['key' => $resource, ...$config];
    }

    public static function navigation(): array
    {
        $items = collect(self::all())
            ->reject(fn (array $config) => (bool) ($config['hidden'] ?? false))
            ->map(fn (array $config, string $key) => ['key' => $key, ...$config])
            ->groupBy('navigation_group');

        return $items->map(function ($group, string $heading) {
            return [
                'heading' => $heading,
                'items' => $group
                    ->map(fn (array $item) => [
                        'key' => $item['key'],
                        'label' => $item['navigation_label'] ?? $item['label'],
                        'icon' => $item['navigation_icon'] ?? null,
                    ])
                    ->values()
                    ->all(),
            ];
        })->values()->all();
    }
}
