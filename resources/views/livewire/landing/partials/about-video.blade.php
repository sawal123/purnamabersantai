<section class="relative z-10 pb-20 pt-12">
    <div class="reveal" style="--delay: 140ms">
        <div class="torn-video">
            <img
                src="{{ asset('landing/assets/sobekan.svg') }}"
                alt=""
                class="torn-video-strip torn-video-strip-top"
            />
            <div class="mx-auto max-w-4xl px-5 lg:px-8">
                <div class="torn-video-frame">
                    <iframe
                        src="{{ $landingSetting?->video_url ? str_replace('watch?v=', 'embed/', $landingSetting->video_url).'?rel=0&modestbranding=1' : 'https://www.youtube.com/embed/yRh8YQ2m1ZU?rel=0&modestbranding=1' }}"
                        title="{{ $landingSetting?->site_name ?? 'Purnama Bersantai' }} video"
                        allow="
                          accelerometer;
                          autoplay;
                          clipboard-write;
                          encrypted-media;
                          gyroscope;
                          picture-in-picture;
                          web-share;
                        "
                        referrerpolicy="strict-origin-when-cross-origin"
                        allowfullscreen
                    >
                    </iframe>
                </div>
            </div>
            <img
                src="{{ asset('landing/assets/sobekan.svg') }}"
                alt=""
                class="torn-video-strip torn-video-strip-bottom"
            />
        </div>
    </div>
</section>
