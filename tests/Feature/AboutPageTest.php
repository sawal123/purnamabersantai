<?php

use App\Models\AboutUs;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('about page renders active dashboard content', function () {
    AboutUs::query()->create([
        'label' => 'About Us',
        'organization_kicker' => 'Crew Profile',
        'organization_title' => 'Database Organization Title',
        'organization_body' => '<p>Database organization body.</p>',
        'history_kicker' => 'Movement Archive',
        'history_title' => 'Database History Title',
        'history_body' => '<p>Database history body.</p>',
        'history_cta_label' => 'Open Archive',
        'history_cta_url' => '/history',
        'is_active' => true,
    ]);

    $response = $this->get(route('landing.about'));

    $response
        ->assertOk()
        ->assertSee('Crew Profile')
        ->assertSee('Database Organization Title')
        ->assertSee('Database organization body.')
        ->assertSee('Movement Archive')
        ->assertSee('Database History Title')
        ->assertSee('Database history body.');
});

test('authenticated users can manage about us from dashboard resource page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard.resource', 'about-us'))
        ->assertOk()
        ->assertSee('About Us')
        ->assertSee('Kelola konten utama halaman About Us');
});
