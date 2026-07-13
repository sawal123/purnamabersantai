<?php

namespace App\Livewire\Landing\Concerns;

use App\Models\ContactChannel;
use App\Models\CountdownSetting;
use App\Models\GalleryMoment;
use App\Models\LandingHeroImage;
use App\Models\LandingMarquee;
use App\Models\LandingSetting;
use App\Models\LandingSectionHeading;
use App\Models\LineupArtist;
use App\Models\MerchandiseProduct;
use App\Models\SponsorPartner;
use App\Models\Ticket;
use App\Models\YoutubeVideo;

trait LoadsLandingContent
{
    protected function landingContent(): array
    {
        $setting = LandingSetting::query()
            ->where('is_active', true)
            ->latest('id')
            ->first();

        return [
            'landingSetting' => $setting,
            'countdownSetting' => CountdownSetting::query()
                ->active()
                ->whereNotNull('target_at')
                ->latest('id')
                ->first(),
            'heroImages' => $setting
                ? $setting->heroImages()->where('is_active', true)->get()
                : LandingHeroImage::query()->where('is_active', true)->ordered()->get(),
            'lineupArtists' => LineupArtist::query()
                ->where('is_active', true)
                ->ordered()
                ->get(),
            'tickets' => Ticket::query()
                ->where('is_active', true)
                ->ordered()
                ->get(),
            'merchandiseProducts' => MerchandiseProduct::query()
                ->with([
                    'images' => fn ($query) => $query->where('is_active', true),
                ])
                ->where('is_active', true)
                ->ordered()
                ->get(),
            'galleryMoments' => GalleryMoment::query()
                ->where('is_active', true)
                ->ordered()
                ->get(),
            'sponsorPartners' => SponsorPartner::query()
                ->where('is_active', true)
                ->ordered()
                ->get(),
            'contactChannels' => ContactChannel::query()
                ->where('is_active', true)
                ->ordered()
                ->get(),
            'landingMarquees' => LandingMarquee::query()
                ->where('is_active', true)
                ->ordered()
                ->get()
                ->keyBy('placement'),
            'landingSectionHeadings' => LandingSectionHeading::query()
                ->where('is_active', true)
                ->ordered()
                ->get()
                ->keyBy('placement'),
            'youtubeVideo' => YoutubeVideo::query()
                ->where('is_active', true)
                ->ordered()
                ->first(),
        ];
    }
}
