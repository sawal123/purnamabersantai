<?php

namespace Database\Seeders;

use App\Models\ContactChannel;
use App\Models\FrequentlyAskedQuestion;
use App\Models\GalleryMoment;
use App\Models\History;
use App\Models\LandingHeroImage;
use App\Models\LandingSetting;
use App\Models\LineupArtist;
use App\Models\MerchandiseProduct;
use App\Models\MerchandiseProductFeature;
use App\Models\MerchandiseProductImage;
use App\Models\SponsorPartner;
use App\Models\Ticket;
use Illuminate\Database\Seeder;

class DashboardDummySeeder extends Seeder
{
    public function run(): void
    {
        $setting = LandingSetting::query()->updateOrCreate(
            ['site_name' => 'Purnama Bersantai'],
            [
                'page_title' => 'Purnama Bersantai 2026',
                'hero_tagline' => 'Musik, bulan, dan akhir pekan yang pelan.',
                'hero_description' => 'Festival musik santai dengan lineup indie, tenant lokal, dan suasana malam tropis.',
                'logo_path' => '/storage/dashboard/brand/contact-card.png',
                'hero_brand_path' => '/storage/dashboard/brand/hero-stage.png',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'footer_description' => 'Purnama Bersantai menghadirkan ruang temu yang hangat untuk musik, komunitas, dan karya lokal.',
                'sponsor_text' => 'Terbuka untuk partner makanan, minuman, komunitas kreatif, dan brand lifestyle.',
                'event_info' => [
                    'date' => '24 Agustus 2026',
                    'venue' => 'Taman Senja, Bandung',
                    'open_gate' => '15:00 WIB',
                ],
                'is_active' => true,
            ],
        );

        foreach ([
            ['image_path' => '/storage/dashboard/brand/hero-stage.png', 'alt_text' => 'Moonlit festival stage', 'sort_order' => 1],
            ['image_path' => '/storage/dashboard/brand/hero-crowd.png', 'alt_text' => 'Festival crowd under warm lights', 'sort_order' => 2],
        ] as $heroImage) {
            LandingHeroImage::query()->updateOrCreate(
                ['image_path' => $heroImage['image_path']],
                ['landing_setting_id' => $setting->id, 'is_active' => true, ...$heroImage],
            );
        }

        foreach ([
            ['tahun' => 2026, 'title' => 'Purnama Bersantai: Full Moon Weekender', 'lokasi' => 'Taman Senja, Bandung', 'capacity' => 8500, 'tanggal_acara' => '2026-08-08'],
            ['tahun' => 2025, 'title' => 'Purnama Bersantai: Moonlit Weekend', 'lokasi' => 'Lapangan Banteng, Jakarta', 'capacity' => 7200, 'tanggal_acara' => '2025-08-09'],
            ['tahun' => 2024, 'title' => 'Purnama Bersantai: City Lights Session', 'lokasi' => 'M Bloc Space, Jakarta', 'capacity' => 5200, 'tanggal_acara' => '2024-08-10'],
            ['tahun' => 2023, 'title' => 'Purnama Bersantai: First Gathering', 'lokasi' => 'Hutan Kota GBK, Jakarta', 'capacity' => 4200, 'tanggal_acara' => '2023-08-12'],
            ['tahun' => 2022, 'title' => 'Purnama Bersantai: Reconnect Stage', 'lokasi' => 'Kiara Artha Park, Bandung', 'capacity' => 3600, 'tanggal_acara' => '2022-08-13'],
            ['tahun' => 2021, 'title' => 'Purnama Bersantai: Backyard Broadcast', 'lokasi' => 'Hybrid Studio, Jakarta', 'capacity' => 1800, 'tanggal_acara' => '2021-08-14'],
            ['tahun' => 2020, 'title' => 'Purnama Bersantai: Home Session', 'lokasi' => 'Online Stream', 'capacity' => 1200, 'tanggal_acara' => '2020-08-08'],
            ['tahun' => 2019, 'title' => 'Purnama Bersantai: Kampung Kreatif', 'lokasi' => 'Kampung Kemang, Jakarta', 'capacity' => 2800, 'tanggal_acara' => '2019-08-10'],
            ['tahun' => 2018, 'title' => 'Purnama Bersantai: Picnic Jam', 'lokasi' => 'Taman Menteng, Jakarta', 'capacity' => 2100, 'tanggal_acara' => '2018-08-11'],
            ['tahun' => 2017, 'title' => 'Purnama Bersantai: Rooftop Chorus', 'lokasi' => 'Rooftop Pasar Santa, Jakarta', 'capacity' => 1400, 'tanggal_acara' => '2017-08-12'],
            ['tahun' => 2016, 'title' => 'Purnama Bersantai: Awal Bulan', 'lokasi' => 'Kedai Halaman, Jakarta', 'capacity' => 650, 'tanggal_acara' => '2016-08-13'],
        ] as $index => $history) {
            $year = $history['tahun'];
            $historyBasePath = "/storage/dashboard/history/{$year}";

            History::query()->updateOrCreate(
                ['tahun' => $year],
                [
                    'order_by' => $index + 1,
                    'content' => "Edisi {$year} menjadi bagian penting perjalanan Purnama Bersantai, mempertemukan musisi lokal, komunitas kreatif, tenant pilihan, dan penonton dalam suasana festival malam yang hangat.\n\nTahun ini menonjolkan ruang festival yang lebih tertata, visual panggung yang lebih berkarakter, serta area komunitas yang memberi tempat bagi pengunjung untuk beristirahat, berbincang, dan menikmati malam dengan ritme yang santai.",
                    'thumbnail' => "{$historyBasePath}/thumbnail.png",
                    'media' => [
                        "{$historyBasePath}/media-stage.png",
                        "{$historyBasePath}/media-crowd.png",
                    ],
                    'festival_galery' => [
                        "{$historyBasePath}/gallery-1.png",
                        "{$historyBasePath}/gallery-2.png",
                        "{$historyBasePath}/gallery-3.png",
                    ],
                    'is_active' => true,
                    ...$history,
                ],
            );
        }

        foreach ([
            ['name' => 'Senja Lestari', 'image_path' => '/storage/dashboard/lineup/artist-senja.png', 'is_featured' => true],
            ['name' => 'Raka Pradana', 'image_path' => '/storage/dashboard/lineup/artist-raka.png', 'is_featured' => true],
            ['name' => 'Nara Swara', 'image_path' => '/storage/dashboard/lineup/artist-nara.png', 'is_featured' => false],
            ['name' => 'Kirana & The Moons', 'image_path' => '/storage/dashboard/lineup/artist-kirana.png', 'is_featured' => false],
        ] as $index => $artist) {
            LineupArtist::query()->updateOrCreate(
                ['name' => $artist['name']],
                [
                    'alt_text' => $artist['name'].' performing on stage',
                    'image_class' => 'object-cover',
                    'sort_order' => $index + 1,
                    'is_active' => true,
                    ...$artist,
                ],
            );
        }

        foreach ([
            ['name' => 'Presale Moonlight', 'batch_label' => 'Batch 1', 'price' => 125000, 'availability_label' => 'Available', 'status' => 'available'],
            ['name' => 'Regular Sunset', 'batch_label' => 'Batch 2', 'price' => 175000, 'availability_label' => 'Limited Seat', 'status' => 'limited'],
            ['name' => 'Duo Santai', 'batch_label' => 'Couple Pack', 'price' => 320000, 'availability_label' => 'Available', 'status' => 'available'],
            ['name' => 'On The Spot', 'batch_label' => 'Gate Ticket', 'price' => 225000, 'availability_label' => 'Coming Soon', 'status' => 'coming_soon'],
        ] as $index => $ticket) {
            Ticket::query()->updateOrCreate(
                ['name' => $ticket['name']],
                [
                    'currency' => 'IDR',
                    'purchase_url' => 'https://purnamabersantai.test/tickets',
                    'purchase_links' => [[
                        'label' => $ticket['batch_label'],
                        'url' => 'https://purnamabersantai.test/tickets',
                    ]],
                    'sort_order' => $index + 1,
                    'is_active' => true,
                    ...$ticket,
                ],
            );
        }

        $products = [
            ['slug' => 'purnama-tee', 'kicker' => 'Signature Drop', 'name' => 'Purnama Tee', 'price' => 185000, 'thumbnail_path' => '/storage/dashboard/merchandise/purnama-tee.png'],
            ['slug' => 'moon-cap', 'kicker' => 'Headwear', 'name' => 'Moon Cap', 'price' => 145000, 'thumbnail_path' => '/storage/dashboard/merchandise/moon-cap.png'],
            ['slug' => 'festival-tote', 'kicker' => 'Daily Carry', 'name' => 'Festival Tote', 'price' => 120000, 'thumbnail_path' => '/storage/dashboard/merchandise/festival-tote.png'],
            ['slug' => 'pin-pack', 'kicker' => 'Collectible', 'name' => 'Pin Pack', 'price' => 65000, 'thumbnail_path' => '/storage/dashboard/merchandise/pin-pack.png'],
        ];

        foreach ($products as $index => $productData) {
            $product = MerchandiseProduct::query()->updateOrCreate(
                ['slug' => $productData['slug']],
                [
                    'currency' => 'IDR',
                    'description' => 'Merchandise resmi festival dengan material nyaman dan desain bertema bulan.',
                    'thumbnail_alt' => $productData['name'],
                    'thumbnail_class' => 'object-cover',
                    'order_url' => 'https://purnamabersantai.test/merchandise',
                    'sort_order' => $index + 1,
                    'is_active' => true,
                    ...$productData,
                ],
            );

            MerchandiseProductImage::query()->updateOrCreate(
                ['merchandise_product_id' => $product->id, 'image_path' => $productData['thumbnail_path']],
                [
                    'alt_text' => $productData['name'],
                    'image_class' => 'object-cover',
                    'sort_order' => 1,
                    'is_active' => true,
                ],
            );

            foreach (['Limited festival edition', 'Ready stock during event', 'Cocok untuk koleksi'] as $featureIndex => $feature) {
                MerchandiseProductFeature::query()->updateOrCreate(
                    ['merchandise_product_id' => $product->id, 'text' => $feature],
                    ['sort_order' => $featureIndex + 1, 'is_active' => true],
                );
            }
        }

        foreach ([
            ['title' => 'Moonlight Crowd', 'username' => '@bulanberisik', 'image_path' => '/storage/dashboard/gallery/moonlight-crowd.png'],
            ['title' => 'Food Booth Friends', 'username' => '@senjajan', 'image_path' => '/storage/dashboard/gallery/food-booth-friends.png'],
            ['title' => 'Acoustic Sunset', 'username' => '@petiksenja', 'image_path' => '/storage/dashboard/gallery/acoustic-sunset.png'],
            ['title' => 'Ticket Hands', 'username' => '@tiketpurnama', 'image_path' => '/storage/dashboard/gallery/ticket-hands.png'],
        ] as $index => $moment) {
            GalleryMoment::query()->updateOrCreate(
                ['title' => $moment['title']],
                [
                    'alt_text' => $moment['title'],
                    'sort_order' => $index + 1,
                    'is_active' => true,
                    ...$moment,
                ],
            );
        }

        foreach ([
            ['name' => 'Kopi Halaman', 'tier' => 'Food Partner', 'url' => 'https://example.com/kopi-halaman'],
            ['name' => 'Loka Creative', 'tier' => 'Creative Partner', 'url' => 'https://example.com/loka-creative'],
            ['name' => 'Ruang Audio', 'tier' => 'Sound Partner', 'url' => 'https://example.com/ruang-audio'],
            ['name' => 'Senja Transit', 'tier' => 'Mobility Partner', 'url' => 'https://example.com/senja-transit'],
        ] as $index => $partner) {
            SponsorPartner::query()->updateOrCreate(
                ['name' => $partner['name']],
                [
                    'logo_path' => '/storage/dashboard/brand/sponsor-board.png',
                    'description' => 'Partner dummy untuk kebutuhan dashboard CRUD dan preview table.',
                    'sort_order' => $index + 1,
                    'is_active' => true,
                    ...$partner,
                ],
            );
        }

        foreach ([
            ['label' => 'Ticketing WhatsApp', 'type' => 'whatsapp', 'value' => '+62 812-2026-0824', 'url' => 'https://wa.me/6281220260824'],
            ['label' => 'Email Partnership', 'type' => 'email', 'value' => 'partner@purnamabersantai.test', 'url' => 'mailto:partner@purnamabersantai.test'],
            ['label' => 'Instagram', 'type' => 'instagram', 'value' => '@purnamabersantai', 'url' => 'https://instagram.com/purnamabersantai'],
            ['label' => 'Website', 'type' => 'website', 'value' => 'purnamabersantai.test', 'url' => 'https://purnamabersantai.test'],
        ] as $index => $channel) {
            ContactChannel::query()->updateOrCreate(
                ['label' => $channel['label'], 'type' => $channel['type']],
                [
                    'description' => 'Kanal kontak dummy untuk kebutuhan tampilan dashboard.',
                    'sort_order' => $index + 1,
                    'is_active' => true,
                    ...$channel,
                ],
            );
        }

        foreach ([
            [
                'question' => 'Apakah saya bisa membeli tiket langsung di lokasi?',
                'answer' => 'Tiket on the spot akan tersedia jika kuota masih ada. Kami menyarankan pembelian online lebih awal agar kamu mendapat harga terbaik dan proses masuk area festival lebih cepat.',
            ],
            [
                'question' => 'Kapan gate Purnama Bersantai dibuka?',
                'answer' => 'Gate festival dibuka mulai pukul 15.00 WIB. Datang lebih awal supaya kamu punya waktu untuk menukar tiket, melihat area tenant, dan memilih spot menonton yang nyaman.',
            ],
            [
                'question' => 'Apakah tersedia merchandise resmi saat acara?',
                'answer' => 'Ya, merchandise resmi tersedia di booth festival selama stok masih ada. Beberapa item juga dapat dipesan melalui halaman merchandise sebelum hari acara.',
            ],
            [
                'question' => 'Bolehkah membawa makanan dan minuman dari luar?',
                'answer' => 'Pengunjung tidak diperkenankan membawa makanan dan minuman dari luar area festival. Di dalam venue tersedia tenant makanan dan minuman pilihan untuk menemani acara.',
            ],
            [
                'question' => 'Apakah anak-anak boleh datang ke festival?',
                'answer' => 'Anak-anak boleh hadir dengan pendamping orang tua atau wali. Pastikan tetap memperhatikan area keramaian, volume suara panggung, dan kenyamanan selama acara berlangsung.',
            ],
            [
                'question' => 'Bagaimana jika hujan saat acara berlangsung?',
                'answer' => 'Festival tetap berjalan selama kondisi aman. Siapkan jas hujan pribadi, ikuti arahan petugas di lokasi, dan pantau kanal resmi untuk informasi terbaru.',
            ],
        ] as $index => $faq) {
            FrequentlyAskedQuestion::query()->updateOrCreate(
                ['question' => $faq['question']],
                [
                    'sort_order' => $index + 1,
                    'is_active' => true,
                    ...$faq,
                ],
            );
        }
    }
}
