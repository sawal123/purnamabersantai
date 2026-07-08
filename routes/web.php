<?php

use App\Livewire\Dashboard\Overview as DashboardOverview;
use App\Livewire\Dashboard\ResourcePage as DashboardResourcePage;
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
Route::livewire('/tickets', Tickets::class)->name('landing.tickets');
Route::livewire('/merchandise', Merch::class)->name('landing.merch');
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
    Route::livewire('dashboard/{resource}', DashboardResourcePage::class)
        ->whereIn('resource', DashboardResourceRegistry::keys())
        ->name('dashboard.resource');
});

require __DIR__.'/settings.php';
