<div
    x-data="{ open: @js($errors->isNotEmpty() || filled($shareMomentTitle) || filled($shareMomentUsername) || filled($shareMomentAltText) || $shareMomentImage !== null) }"
    x-effect="document.body.classList.toggle('modal-open', open)"
    x-on:share-moment-saved.window="open = false"
    class="relative -mt-8 pb-24"
>
    <div class="relative z-10 mx-auto max-w-7xl px-5 text-center lg:px-8">
        @if (session('share-moment-status'))
            <div class="mx-auto mb-6 max-w-3xl rounded-[1.5rem] border border-emerald-300/20 bg-emerald-400/10 px-5 py-4 text-sm font-medium text-emerald-100 shadow-[0_18px_48px_rgba(0,0,0,0.2)]">
                {{ session('share-moment-status') }}
            </div>
        @endif

        <button
            type="button"
            class="inline-flex items-center justify-center rounded-2xl bg-[#2f2e2e] px-7 py-3 font-display text-3xl uppercase tracking-[0.08em] text-white transition hover:-translate-y-1 hover:bg-[#242323]"
            x-on:click="open = true"
        >
            Share Your Moment
        </button>
    </div>

    <div
        x-bind:class="open ? 'fixed' : 'hidden'"
        class="minimal-scrollbar inset-0 z-[120] overflow-y-auto overscroll-contain"
        x-on:keydown.escape.window="open = false"
        aria-hidden="true"
    >
        <button
            type="button"
            class="fixed inset-0 bg-black/75 backdrop-blur-md"
            aria-label="Close share moment modal"
            x-on:click="open = false"
        ></button>

        <div class="relative z-10 flex min-h-full items-start justify-center p-4 sm:p-6">
            <div class="relative my-6 w-full max-w-2xl overflow-hidden rounded-[2rem] border border-white/12 bg-[linear-gradient(180deg,rgba(28,8,10,0.98)_0%,rgba(10,3,5,0.98)_100%)] shadow-[0_24px_90px_rgba(0,0,0,0.45)] sm:my-10">
                <button
                    type="button"
                    class="absolute right-4 top-4 inline-flex h-11 w-11 items-center justify-center rounded-full border border-white/15 bg-white/6 text-white transition hover:-translate-y-0.5 hover:bg-white/12"
                    aria-label="Close share moment modal"
                    x-on:click="open = false"
                >
                    <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" aria-hidden="true">
                        <path d="M6 6L18 18M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                </button>

                <div class="p-6 sm:p-8">
                    <p class="text-sm font-semibold uppercase tracking-[0.24em] text-[#fff700]/80">
                        Share Your Moment
                    </p>
                    <h3 class="mt-3 font-display text-4xl uppercase tracking-[0.08em] text-white sm:text-5xl">
                        Send Your Festival Memory
                    </h3>
                    <p class="mt-3 max-w-2xl text-base leading-relaxed text-white/70">
                        Isi data singkat di bawah ini. Submission akan masuk dulu untuk direview sebelum ditampilkan ke galeri publik.
                    </p>

                    <form class="mt-8 space-y-5" wire:submit="submitShareMoment">
                        <div class="grid gap-5 sm:grid-cols-2">
                            <label class="block sm:col-span-2">
                                <span class="mb-2 block text-sm font-semibold uppercase tracking-[0.18em] text-white/70">Moment Title</span>
                                <input
                                    type="text"
                                    class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-base text-white outline-none transition placeholder:text-white/35 focus:border-[#fff700]/60 focus:bg-white/8"
                                    placeholder="Acoustic Sunset"
                                    wire:model="shareMomentTitle"
                                />
                                @error('shareMomentTitle')
                                    <span class="mt-2 block text-sm text-[#fff700]">{{ $message }}</span>
                                @enderror
                            </label>

                            <label class="block">
                                <span class="mb-2 block text-sm font-semibold uppercase tracking-[0.18em] text-white/70">Username</span>
                                <input
                                    type="text"
                                    class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-base text-white outline-none transition placeholder:text-white/35 focus:border-[#fff700]/60 focus:bg-white/8"
                                    placeholder="@namakamu"
                                    wire:model="shareMomentUsername"
                                />
                                @error('shareMomentUsername')
                                    <span class="mt-2 block text-sm text-[#fff700]">{{ $message }}</span>
                                @enderror
                            </label>

                            <label class="block">
                                <span class="mb-2 block text-sm font-semibold uppercase tracking-[0.18em] text-white/70">Alt Text</span>
                                <input
                                    type="text"
                                    class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-base text-white outline-none transition placeholder:text-white/35 focus:border-[#fff700]/60 focus:bg-white/8"
                                    placeholder="Teman-teman menikmati musik saat sunset"
                                    wire:model="shareMomentAltText"
                                />
                                @error('shareMomentAltText')
                                    <span class="mt-2 block text-sm text-[#fff700]">{{ $message }}</span>
                                @enderror
                            </label>

                            <div class="block sm:col-span-2">
                                <div class="mb-2 flex items-center justify-between gap-3">
                                    <span class="block text-sm font-semibold uppercase tracking-[0.18em] text-white/70">Upload Image</span>
                                    @if ($this->shareMomentPreviewUrl())
                                        <button
                                            type="button"
                                            class="text-xs font-semibold uppercase tracking-[0.16em] text-[#fff700]/80 transition hover:text-white"
                                            wire:click="removeShareMomentImage"
                                        >
                                            Remove
                                        </button>
                                    @endif
                                </div>

                                @if ($this->shareMomentPreviewUrl())
                                    <div class="mb-4 overflow-hidden rounded-[1.5rem] border border-white/10 bg-white/5">
                                        <img
                                            src="{{ $this->shareMomentPreviewUrl() }}"
                                            alt="Preview uploaded moment image"
                                            class="h-64 w-full object-cover"
                                        />
                                    </div>
                                @endif

                                <label class="group flex cursor-pointer flex-col items-center justify-center rounded-[1.5rem] border border-dashed border-white/20 bg-white/5 px-4 py-8 text-center transition hover:border-[#fff700]/60 hover:bg-white/8">
                                    <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-full border border-white/10 bg-white/6 text-white/80">
                                        <svg viewBox="0 0 24 24" fill="none" class="h-7 w-7" aria-hidden="true">
                                            <path d="M12 16V4M12 4L7 9M12 4L17 9M5 18.5C5 17.12 6.12 16 7.5 16H16.5C17.88 16 19 17.12 19 18.5C19 19.88 17.88 21 16.5 21H7.5C6.12 21 5 19.88 5 18.5Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </div>

                                    <span class="text-base font-semibold text-white">Upload foto moment kamu</span>
                                    <span class="mt-1 text-sm text-white/55">PNG, JPG, JPEG, atau WEBP maksimal 4MB</span>

                                    <input
                                        type="file"
                                        accept="image/png,image/jpeg,image/jpg,image/webp"
                                        class="sr-only"
                                        wire:model="shareMomentImage"
                                    >
                                </label>

                                <div class="mt-3 text-sm text-white/45" wire:loading wire:target="shareMomentImage">
                                    Uploading image...
                                </div>

                                @error('shareMomentImage')
                                    <span class="mt-2 block text-sm text-[#fff700]">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="flex flex-col gap-3 border-t border-white/10 pt-5 sm:flex-row sm:items-center sm:justify-between">
                            <p class="text-sm text-white/50">
                                Dengan submit ini, moment kamu belum langsung tayang.
                            </p>
                            <div class="flex flex-col gap-3 sm:flex-row">
                                <button
                                    type="button"
                                    class="rounded-2xl border border-white/15 px-5 py-3 text-sm font-semibold uppercase tracking-[0.18em] text-white transition hover:bg-white/8"
                                    x-on:click="open = false"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    class="rounded-2xl bg-ember px-6 py-3 font-display text-xl leading-none uppercase tracking-[0.06em] text-white whitespace-nowrap transition hover:-translate-y-1 hover:bg-[#fff700] hover:text-[#2f2e2e] disabled:cursor-not-allowed disabled:opacity-60"
                                    wire:loading.attr="disabled"
                                    wire:target="shareMomentImage,submitShareMoment"
                                >
                                    <span wire:loading.remove wire:target="submitShareMoment">Send Moment</span>
                                    <span wire:loading wire:target="submitShareMoment">Sending...</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
