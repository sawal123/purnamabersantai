<section class="w-full space-y-6">
    @php
        $quickLinks = [
            ['label' => 'Landing Settings', 'description' => 'Identitas website, hero copy, footer, dan info event.', 'url' => route('dashboard.resource', 'landing-setting'), 'icon' => 'adjustments-horizontal'],
            ['label' => 'SEO Settings', 'description' => 'Meta title, description, preview sharing, dan robots.', 'url' => route('dashboard.resource', 'seo-setting'), 'icon' => 'magnifying-glass'],
            ['label' => 'Lineup Artists', 'description' => 'Daftar artis, foto, featured, dan status aktif.', 'url' => route('dashboard.resource', 'lineup-artist'), 'icon' => 'microphone'],
            ['label' => 'Tickets', 'description' => 'Batch tiket, harga, status, dan link pembelian.', 'url' => route('dashboard.resource', 'ticket'), 'icon' => 'ticket'],
            ['label' => 'Merchandise', 'description' => 'Produk, harga, kategori, stok, dan gallery gambar.', 'url' => route('dashboard.merchandise-product'), 'icon' => 'shopping-bag'],
            ['label' => 'Not Found Images', 'description' => 'Gambar fallback saat section belum punya data.', 'url' => route('dashboard.resource', 'not-found-image'), 'icon' => 'photo'],
        ];

        $imageGuides = [
            ['module' => 'Hero Images', 'size' => '1920 x 1080 px', 'note' => 'Gunakan foto horizontal yang jelas dan tidak terlalu gelap.'],
            ['module' => 'Not Found Images', 'size' => '1858 x 585 px', 'note' => 'Dipakai sebagai gambar kosong/fallback per section.'],
            ['module' => 'Landing Body Elements', 'size' => 'PNG/WebP transparan', 'note' => 'Maksimal 1MB, cocok untuk dekorasi sisi kanan/kiri.'],
            ['module' => 'Lineup Artists', 'size' => 'Minimal 800 x 1000 px', 'note' => 'Foto portrait lebih aman untuk kartu artis.'],
            ['module' => 'Merchandise', 'size' => 'Minimal 1000 x 1000 px', 'note' => 'Foto produk square, background rapi, gambar pertama menjadi thumbnail.'],
            ['module' => 'Gallery Moments', 'size' => 'Minimal 1200 x 800 px', 'note' => 'Foto landscape atau square dengan momen yang mudah dikenali.'],
            ['module' => 'History', 'size' => 'Minimal 1200 x 800 px', 'note' => 'Thumbnail, media, dan gallery untuk arsip event.'],
        ];

        $moduleGuides = [
            [
                'title' => 'Website',
                'items' => [
                    'SEO Settings: isi meta title, meta description, OG image, dan pastikan Active menyala.',
                    'Landing Settings: atur nama website, tagline, deskripsi hero, logo, footer, sponsor text, dan info event.',
                    'Section Headings: ubah judul, kicker, dan subtitle tiap section landing page.',
                    'Landing Marquees: kelola teks berjalan seperti pengumuman, tagline, atau highlight event.',
                ],
            ],
            [
                'title' => 'Event Content',
                'items' => [
                    'Countdown: isi target tanggal event jika countdown ingin tampil.',
                    'Lineup Artists: tambah nama artis, foto, urutan tampil, dan nyalakan Active.',
                    'Tickets: isi nama batch, harga, status, dan link pembelian. Gunakan Sold Out jika tiket habis.',
                    'Histories: isi arsip event. Jika kosong, halaman About dan History akan menampilkan state kosong.',
                    'Rundown & Map: buat kategori lalu upload gambar rundown atau map pada kategori tersebut.',
                    'FAQ: isi pertanyaan yang sering ditanyakan pengunjung.',
                ],
            ],
            [
                'title' => 'Media & Merchandise',
                'items' => [
                    'Hero Images: upload gambar utama landing page.',
                    'Gallery Moments: upload momen festival, judul, username, dan deskripsi singkat.',
                    'Songs dan Spotify Playlists: kelola musik atau playlist yang ingin ditampilkan.',
                    'YouTube Videos: isi video embed atau link YouTube resmi.',
                    'Merchandise Categories: buat kategori produk sebelum menambah produk merchandise.',
                    'Merchandise Products: isi nama produk, harga, stok, deskripsi, ukuran, warna, dan gallery gambar.',
                ],
            ],
            [
                'title' => 'Visual Elements',
                'items' => [
                    'Ticket Card Elements: dekorasi visual untuk kartu tiket.',
                    'Landing Body Elements: dekorasi sisi kanan/kiri untuk halaman tertentu.',
                    'Not Found Images: gambar yang tampil saat section belum punya data.',
                    'Gunakan Page Section yang sesuai agar gambar muncul di halaman yang benar.',
                ],
            ],
            [
                'title' => 'Partnership & Contact',
                'items' => [
                    'Sponsor Partners: isi nama sponsor, tier, logo, URL, dan status Active.',
                    'Contact Channels: isi WhatsApp, email, Instagram, TikTok, website, atau kanal kontak lain.',
                    'Pastikan URL diawali https:// agar link aman dibuka pengunjung.',
                ],
            ],
        ];
    @endphp

    <div class="overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-600 via-violet-600 to-fuchsia-600 p-6 text-white shadow-xl shadow-indigo-500/15 sm:p-8">
        <div class="grid gap-6 lg:grid-cols-[minmax(0,1.2fr)_minmax(18rem,0.8fr)] lg:items-end">
            <div class="max-w-3xl">
                <span class="inline-flex rounded-full bg-white/15 px-3 py-1 text-xs font-bold ring-1 ring-white/20">Client Guide</span>
                <h1 class="mt-4 text-3xl font-extrabold tracking-tight sm:text-4xl">Dokumentasi Dashboard</h1>
                <p class="mt-3 text-sm leading-6 text-indigo-100 md:text-base">
                    Panduan singkat untuk mengelola konten landing page Purnama Bersantai, mulai dari update gambar, ticketing, lineup, merchandise, sampai SEO.
                </p>
            </div>

            <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                <p class="text-xs uppercase tracking-[0.2em] text-indigo-100">Alur Utama</p>
                <ol class="mt-3 space-y-2 text-sm font-semibold text-white">
                    <li>1. Tambah atau edit data.</li>
                    <li>2. Pastikan Active menyala.</li>
                    <li>3. Cek hasil di landing page.</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @foreach ($quickLinks as $link)
            <a href="{{ $link['url'] }}" wire:navigate class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-indigo-300 hover:bg-indigo-50 dark:border-slate-800 dark:bg-slate-900 dark:hover:border-indigo-500/60 dark:hover:bg-indigo-500/10">
                <div class="flex items-start gap-4">
                    <span class="grid size-11 shrink-0 place-items-center rounded-2xl bg-indigo-100 text-indigo-700 dark:bg-indigo-500/15 dark:text-indigo-300">
                        <x-ui-dashboard.icon :name="$link['icon']" class="size-5" />
                    </span>
                    <span class="min-w-0">
                        <span class="block font-bold text-slate-950 dark:text-white">{{ $link['label'] }}</span>
                        <span class="mt-1 block text-sm leading-6 text-slate-500 dark:text-slate-400">{{ $link['description'] }}</span>
                    </span>
                </div>
            </a>
        @endforeach
    </div>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.15fr)_minmax(22rem,0.85fr)]">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-indigo-700 dark:text-indigo-300">Panduan Modul</p>
            <h2 class="mt-2 text-2xl font-bold text-slate-950 dark:text-white">Cara mengelola konten</h2>

            <div class="mt-6 space-y-4">
                @foreach ($moduleGuides as $guide)
                    <details class="group rounded-2xl border border-slate-200 bg-slate-50 p-4 open:bg-white dark:border-slate-800 dark:bg-slate-950/60 dark:open:bg-slate-900">
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-4 font-bold text-slate-950 dark:text-white">
                            {{ $guide['title'] }}
                            <span class="grid size-8 place-items-center rounded-xl bg-white text-slate-500 transition group-open:rotate-45 dark:bg-slate-800 dark:text-slate-300">
                                <x-ui-dashboard.icon name="x" class="size-4" />
                            </span>
                        </summary>
                        <ul class="mt-4 space-y-3 text-sm leading-6 text-slate-600 dark:text-slate-300">
                            @foreach ($guide['items'] as $item)
                                <li class="flex gap-3">
                                    <span class="mt-2 size-1.5 shrink-0 rounded-full bg-indigo-500"></span>
                                    <span>{{ $item }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </details>
                @endforeach
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-indigo-700 dark:text-indigo-300">Checklist</p>
                <h2 class="mt-2 text-2xl font-bold text-slate-950 dark:text-white">Sebelum publish</h2>
                <div class="mt-5 space-y-3 text-sm leading-6 text-slate-600 dark:text-slate-300">
                    <label class="flex gap-3 rounded-2xl bg-slate-50 p-3 dark:bg-slate-950/60">
                        <input type="checkbox" class="mt-1 rounded border-slate-300 text-indigo-600">
                        <span>SEO Settings sudah aktif dan punya OG image.</span>
                    </label>
                    <label class="flex gap-3 rounded-2xl bg-slate-50 p-3 dark:bg-slate-950/60">
                        <input type="checkbox" class="mt-1 rounded border-slate-300 text-indigo-600">
                        <span>Landing Settings, hero image, dan footer sudah sesuai.</span>
                    </label>
                    <label class="flex gap-3 rounded-2xl bg-slate-50 p-3 dark:bg-slate-950/60">
                        <input type="checkbox" class="mt-1 rounded border-slate-300 text-indigo-600">
                        <span>Ticket, lineup, sponsor, gallery, dan contact yang perlu tampil sudah Active.</span>
                    </label>
                    <label class="flex gap-3 rounded-2xl bg-slate-50 p-3 dark:bg-slate-950/60">
                        <input type="checkbox" class="mt-1 rounded border-slate-300 text-indigo-600">
                        <span>Landing page sudah dicek ulang dari menu Lihat Landing Page.</span>
                    </label>
                </div>
            </div>

            <div class="rounded-3xl border border-amber-200 bg-amber-50 p-6 shadow-sm dark:border-amber-500/20 dark:bg-amber-500/10">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-amber-700 dark:text-amber-300">Catatan Penting</p>
                <ul class="mt-4 space-y-3 text-sm leading-6 text-amber-800 dark:text-amber-100">
                    <li>Data tidak tampil di landing page jika Active mati.</li>
                    <li>Jika section kosong, sistem bisa menampilkan Not Found Image sesuai Page Section.</li>
                    <li>Hapus data hanya jika benar-benar tidak dipakai. Untuk menyembunyikan sementara, matikan Active.</li>
                    <li>Gunakan gambar terkompresi agar website tetap cepat dibuka.</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-indigo-700 dark:text-indigo-300">Asset Guide</p>
                <h2 class="mt-2 text-2xl font-bold text-slate-950 dark:text-white">Ukuran gambar yang disarankan</h2>
            </div>
            <p class="text-sm text-slate-500 dark:text-slate-400">Maksimal upload mengikuti aturan tiap modul.</p>
        </div>

        <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-800">
            <div class="grid grid-cols-[minmax(8rem,0.75fr)_minmax(8rem,0.55fr)_minmax(0,1fr)] bg-slate-50 text-xs font-extrabold uppercase tracking-[0.16em] text-slate-500 dark:bg-slate-800/60 dark:text-slate-400">
                <div class="px-4 py-3">Modul</div>
                <div class="px-4 py-3">Ukuran</div>
                <div class="px-4 py-3">Catatan</div>
            </div>
            <div class="divide-y divide-slate-200 dark:divide-slate-800">
                @foreach ($imageGuides as $guide)
                    <div class="grid gap-2 px-4 py-4 text-sm text-slate-600 dark:text-slate-300 sm:grid-cols-[minmax(8rem,0.75fr)_minmax(8rem,0.55fr)_minmax(0,1fr)] sm:gap-0">
                        <div class="font-semibold text-slate-950 dark:text-white">{{ $guide['module'] }}</div>
                        <div>{{ $guide['size'] }}</div>
                        <div>{{ $guide['note'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-indigo-700 dark:text-indigo-300">Troubleshooting</p>
        <h2 class="mt-2 text-2xl font-bold text-slate-950 dark:text-white">Jika konten tidak muncul</h2>
        <div class="mt-5 grid gap-4 md:grid-cols-2">
            <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-950/60">
                <p class="font-bold text-slate-950 dark:text-white">Cek status Active</p>
                <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300">Sebagian besar konten hanya tampil jika status Active menyala.</p>
            </div>
            <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-950/60">
                <p class="font-bold text-slate-950 dark:text-white">Cek Page Section</p>
                <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300">Body Element dan Not Found Image harus memakai section yang sesuai dengan halaman tujuan.</p>
            </div>
            <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-950/60">
                <p class="font-bold text-slate-950 dark:text-white">Cek urutan data</p>
                <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300">Field Sort Order atau Order By menentukan posisi tampil pada beberapa modul.</p>
            </div>
            <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-950/60">
                <p class="font-bold text-slate-950 dark:text-white">Cek format file</p>
                <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300">Gunakan JPG, PNG, JPEG, atau WEBP sesuai modul. Pastikan ukuran file tidak melebihi batas upload.</p>
            </div>
        </div>
    </div>
</section>
