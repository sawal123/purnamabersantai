<?php

use App\Livewire\Dashboard\Overview as DashboardOverview;
use App\Livewire\Dashboard\LandingMarqueePage as DashboardLandingMarqueePage;
use App\Livewire\Dashboard\LandingSectionHeadingPage as DashboardLandingSectionHeadingPage;
use App\Livewire\Dashboard\MerchandiseProductPage as DashboardMerchandiseProductPage;
use App\Livewire\Dashboard\ResourcePage as DashboardResourcePage;
use App\Livewire\Dashboard\SongPage as DashboardSongPage;
use App\Livewire\Dashboard\UserPage as DashboardUserPage;
use App\Livewire\Dashboard\YoutubePage as DashboardYoutubePage;
use App\Livewire\Landing\About;
use App\Livewire\Landing\Contact;
use App\Livewire\Landing\Faq;
use App\Livewire\Landing\Gallery;
use App\Livewire\Landing\History;
use App\Livewire\Landing\HistoryDetail;
use App\Livewire\Landing\Home;
use App\Livewire\Landing\Lineup;
use App\Livewire\Landing\Merch;
use App\Livewire\Landing\Playlist;
use App\Livewire\Landing\RundownMap;
use App\Livewire\Landing\SponsorPartners;
use App\Livewire\Landing\Tickets;
use App\Support\DashboardResourceRegistry;
use Illuminate\Support\Facades\Route;

Route::livewire('/', Home::class)->name('home');
Route::livewire('/lineup', Lineup::class)->name('landing.lineup');
Route::livewire('/ticket', Tickets::class)->name('landing.ticket');
Route::livewire('/tickets', Tickets::class)->name('landing.tickets');
Route::livewire('/merchandise', Merch::class)->name('landing.merch');
Route::livewire('/merchandise/{productSlug}', Merch::class)->name('landing.merch.show');
Route::livewire('/gallery', Gallery::class)->name('landing.gallery');
Route::livewire('/playlist', Playlist::class)->name('landing.playlist');
Route::livewire('/rundown-map', RundownMap::class)->name('landing.rundown-map');
Route::livewire('/about', About::class)->name('landing.about');
Route::livewire('/history', History::class)->name('landing.history');
Route::livewire('/history/{title}', HistoryDetail::class)->name('landing.history.show');
Route::livewire('/sponsor-partners', SponsorPartners::class)->name('landing.sponsors');
Route::livewire('/contact', Contact::class)->name('landing.contact');
Route::livewire('/faq', Faq::class)->name('landing.faq');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('dashboard', DashboardOverview::class)->name('dashboard');
    Route::livewire('dashboard/merchandise-product', DashboardMerchandiseProductPage::class)
        ->defaults('resource', 'merchandise-product')
        ->name('dashboard.merchandise-product');
    Route::livewire('dashboard/song', DashboardSongPage::class)
        ->defaults('resource', 'song')
        ->name('dashboard.song');
    Route::livewire('dashboard/landing-marquee', DashboardLandingMarqueePage::class)
        ->defaults('resource', 'landing-marquee')
        ->name('dashboard.landing-marquee');
    Route::livewire('dashboard/landing-section-heading', DashboardLandingSectionHeadingPage::class)
        ->defaults('resource', 'landing-section-heading')
        ->name('dashboard.landing-section-heading');
    Route::livewire('dashboard/youtube', DashboardYoutubePage::class)
        ->defaults('resource', 'youtube')
        ->name('dashboard.youtube');
    Route::livewire('dashboard/user', DashboardUserPage::class)
        ->defaults('resource', 'user')
        ->name('dashboard.user');

    Route::livewire('dashboard/{resource}', DashboardResourcePage::class)
        ->whereIn('resource', DashboardResourceRegistry::keys())
        ->name('dashboard.resource');
});

require __DIR__.'/settings.php';
