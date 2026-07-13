@if ($youtubeVideo?->embed_src)
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
                            src="{{ $youtubeVideo->embed_src }}"
                            title="{{ $youtubeVideo->aria_label ?: $youtubeVideo->title }}"
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
@endif
