<?php

namespace Database\Seeders;

use App\Models\AboutUs;
use Illuminate\Database\Seeder;

class AboutUsSeeder extends Seeder
{
    public function run(): void
    {
        AboutUs::query()->updateOrCreate(
            ['label' => 'About Us'],
            [
                'organization_kicker' => 'Who We Are',
                'organization_title' => 'Organisasi di Balik Purnama Bersantai',
                'organization_body' => '<p>Purnama Bersantai adalah organisasi kreatif yang berfokus pada pengembangan festival, kolaborasi komunitas, dan pengalaman hiburan yang terasa hangat, dekat, dan relevan dengan audiens muda perkotaan.</p><p>Kami membangun ruang temu antara musisi, pelaku UMKM, komunitas, sponsor, dan penonton dalam satu ekosistem acara yang tidak hanya menghadirkan hiburan, tetapi juga membuka peluang pertumbuhan bersama.</p>',
                'history_kicker' => 'Our History',
                'history_title' => 'Tumbuh Bersama Komunitas',
                'history_body' => '<p>Perjalanan Purnama Bersantai dibangun sedikit demi sedikit, dari gathering yang hangat hingga menjadi festival yang punya identitas kuat dan ruang kolaborasi yang lebih luas.</p>',
                'history_cta_label' => 'See All Histories',
                'history_cta_url' => '/history',
                'is_active' => true,
            ],
        );
    }
}
