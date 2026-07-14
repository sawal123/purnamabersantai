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
use App\Models\TicketCardElement;
use App\Models\YoutubeVideo;
use Illuminate\Support\Facades\Schema;

trait LoadsLandingContent
{
    protected function landingContent(): array
    {
        $setting = LandingSetting::query()
            ->where('is_active', true)
            ->latest('id')
            ->first();

        $landingMarquees = Schema::hasTable('landing_marquees')
            ? LandingMarquee::query()
                ->where('is_active', true)
                ->ordered()
                ->get()
                ->keyBy('placement')
            : collect();

        $landingSectionHeadings = Schema::hasTable('landing_section_headings')
            ? LandingSectionHeading::query()
                ->where('is_active', true)
                ->ordered()
                ->get()
                ->keyBy('placement')
            : collect();

        $youtubeVideo = Schema::hasTable('youtube_videos')
            ? YoutubeVideo::query()
                ->where('is_active', true)
                ->ordered()
                ->first()
            : null;

        $ticketCardElements = Schema::hasTable('ticket_card_elements')
            ? TicketCardElement::query()
                ->where('is_active', true)
                ->whereNotNull('image_path')
                ->ordered()
                ->get()
            : collect();

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
            'ticketCardElements' => $ticketCardElements,
            'merchandiseProducts' => MerchandiseProduct::query()
                ->with([
                    'images' => fn ($query) => $query->where('is_active', true),
                    'category',
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
            'landingMarquees' => $landingMarquees,
            'landingSectionHeadings' => $landingSectionHeadings,
            'youtubeVideo' => $youtubeVideo,
        ];
    }
}
