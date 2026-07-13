<?php

namespace Database\Seeders;

use App\Models\LandingSectionHeading;
use Illuminate\Database\Seeder;

class LandingSectionHeadingSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->defaults() as $heading) {
            LandingSectionHeading::query()->updateOrCreate(
                ['placement' => $heading['placement']],
                $heading,
            );
        }
    }

    protected function defaults(): array
    {
        return [
            [
                'placement' => 'lineup',
                'label' => 'Lineup Section',
                'kicker' => null,
                'title' => 'Lineup',
                'highlight_text' => null,
                'after_highlight_text' => null,
                'subtitle' => 'Temukan performer favoritmu dan jelajahi deretan artis yang akan menghidupkan panggung Purnama Bersantai.',
                'sort_order' => 10,
                'is_active' => true,
            ],
            [
                'placement' => 'tickets',
                'label' => 'Tickets Section',
                'kicker' => 'Official Event Pass',
                'title' => 'Get',
                'highlight_text' => 'Your Ticket',
                'after_highlight_text' => 'Now',
                'subtitle' => 'Pilih pass resmi Purnama Bersantai dan amankan aksesmu sebelum kuota habis.',
                'sort_order' => 20,
                'is_active' => true,
            ],
            [
                'placement' => 'merchandise',
                'label' => 'Merchandise Section',
                'kicker' => null,
                'title' => 'Merchandise Drop',
                'highlight_text' => null,
                'after_highlight_text' => null,
                'subtitle' => 'Pilih koleksi favoritmu lalu buka detail produknya untuk lihat gallery, deskripsi, dan langsung order.',
                'sort_order' => 30,
                'is_active' => true,
            ],
            [
                'placement' => 'gallery',
                'label' => 'Gallery Section',
                'kicker' => null,
                'title' => 'Beautiful Moments',
                'highlight_text' => null,
                'after_highlight_text' => null,
                'subtitle' => 'Relive the night, one moment at a time.',
                'sort_order' => 40,
                'is_active' => true,
            ],
            [
                'placement' => 'sponsors',
                'label' => 'Sponsor Page',
                'kicker' => 'Sponsor & Partner',
                'title' => 'Support The Moonlit Movement',
                'highlight_text' => null,
                'after_highlight_text' => null,
                'subtitle' => 'Brand, komunitas, dan partner yang ikut menjaga Purnama Bersantai tetap hidup.',
                'sort_order' => 50,
                'is_active' => true,
            ],
            [
                'placement' => 'contact',
                'label' => 'Contact Page',
                'kicker' => 'Official Contact',
                'title' => 'Talk To The Crew',
                'highlight_text' => null,
                'after_highlight_text' => null,
                'subtitle' => 'Pilih kanal resmi untuk ticketing, merchandise, partnership, atau update festival terbaru.',
                'sort_order' => 60,
                'is_active' => true,
            ],
            [
                'placement' => 'faq',
                'label' => 'FAQ Page',
                'kicker' => 'Need Answers?',
                'title' => 'Frequently Asked Questions',
                'highlight_text' => null,
                'after_highlight_text' => null,
                'subtitle' => 'Temukan jawaban cepat seputar festival, tiket, merchandise, dan pengalaman Purnama Bersantai.',
                'sort_order' => 70,
                'is_active' => true,
            ],
            [
                'placement' => 'about',
                'label' => 'About Page',
                'kicker' => 'About the Festival',
                'title' => 'Purnama Bersantai',
                'highlight_text' => null,
                'after_highlight_text' => null,
                'subtitle' => 'Festival musik santai dengan lineup indie, tenant lokal, dan suasana malam tropis.',
                'sort_order' => 80,
                'is_active' => true,
            ],
            [
                'placement' => 'playlist',
                'label' => 'Playlist Page',
                'kicker' => 'Official Playlist',
                'title' => 'Soundtrack For The Moon',
                'highlight_text' => null,
                'after_highlight_text' => null,
                'subtitle' => 'Putar lagu pilihan untuk masuk ke mood Purnama Bersantai sebelum hari festival tiba.',
                'sort_order' => 90,
                'is_active' => true,
            ],
            [
                'placement' => 'rundown_map',
                'label' => 'Rundown & Map Page',
                'kicker' => 'Plan Your Night',
                'title' => 'Rundown & Map',
                'highlight_text' => null,
                'after_highlight_text' => null,
                'subtitle' => 'Cek susunan acara dan peta lokasi agar perjalananmu di Purnama Bersantai lebih nyaman.',
                'sort_order' => 100,
                'is_active' => true,
            ],
            [
                'placement' => 'history',
                'label' => 'History Page',
                'kicker' => 'Festival Archive',
                'title' => 'Histories',
                'highlight_text' => null,
                'after_highlight_text' => null,
                'subtitle' => 'Lihat perjalanan Purnama Bersantai dari waktu ke waktu.',
                'sort_order' => 110,
                'is_active' => true,
            ],
        ];
    }
}
