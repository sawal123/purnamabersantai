<?php

use App\Models\History;
use App\Models\MerchandiseProduct;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('sitemap returns public landing page urls', function () {
    $response = $this->get(route('sitemap'));

    $response
        ->assertOk()
        ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
        ->assertSee('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', false)
        ->assertSee(route('home'), false)
        ->assertSee(route('landing.lineup'), false)
        ->assertSee(route('landing.tickets'), false)
        ->assertSee(route('landing.faq'), false);
});

test('sitemap includes active dynamic urls only', function () {
    MerchandiseProduct::query()->create([
        'slug' => 'moon-tee',
        'name' => 'Moon Tee',
        'price' => 185000,
        'is_active' => true,
    ]);

    MerchandiseProduct::query()->create([
        'slug' => 'hidden-tee',
        'name' => 'Hidden Tee',
        'price' => 185000,
        'is_active' => false,
    ]);

    History::query()->create([
        'title' => 'Purnama Bersantai 2026',
        'tahun' => 2026,
        'lokasi' => 'Jakarta',
        'is_active' => true,
    ]);

    History::query()->create([
        'title' => 'Hidden History',
        'tahun' => 2025,
        'lokasi' => 'Jakarta',
        'is_active' => false,
    ]);

    $response = $this->get(route('sitemap'));

    $response
        ->assertOk()
        ->assertSee(route('landing.merch.show', ['productSlug' => 'moon-tee']), false)
        ->assertSee(route('landing.history.show', ['title' => 'purnama-bersantai-2026']), false)
        ->assertDontSee('hidden-tee', false)
        ->assertDontSee('hidden-history', false);
});
