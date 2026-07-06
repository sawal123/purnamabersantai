<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
    'batch_label',
    'price',
    'currency',
    'availability_label',
    'status',
    'purchase_url',
    'purchase_links',
    'sort_order',
    'is_active',
])]
class Ticket extends Model
{
    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'is_active' => 'boolean',
            'purchase_links' => 'array',
        ];
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function purchaseOptions(): array
    {
        $options = collect($this->purchase_links)
            ->map(function ($item) {
                if (is_string($item)) {
                    return [
                        'label' => '',
                        'url' => trim($item),
                    ];
                }

                if (! is_array($item)) {
                    return null;
                }

                return [
                    'label' => trim((string) ($item['label'] ?? '')),
                    'url' => trim((string) ($item['url'] ?? '')),
                ];
            })
            ->filter(fn (?array $item) => $item !== null && filled($item['url']))
            ->values()
            ->all();

        if ($options !== []) {
            return $options;
        }

        if (blank($this->purchase_url)) {
            return [];
        }

        return [[
            'label' => $this->batch_label ?: $this->name,
            'url' => $this->purchase_url,
        ]];
    }
}
