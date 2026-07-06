@props([
    'show' => false,
    'title' => '',
    'description' => null,
    'closeAction' => null,
    'maxWidth' => 'max-w-4xl',
])

@if ($show)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-950/70 p-4 backdrop-blur-sm">
        <div class="w-full {{ $maxWidth }} overflow-hidden rounded-3xl border border-zinc-200 bg-white shadow-2xl dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-start justify-between gap-4 border-b border-zinc-200 px-6 py-5 dark:border-zinc-700">
                <div>
                    <h2 class="text-xl font-semibold text-zinc-950 dark:text-white">{{ $title }}</h2>
                    @if ($description)
                        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ $description }}</p>
                    @endif
                </div>

                @if ($closeAction)
                    <button type="button" wire:click="{{ $closeAction }}" class="rounded-full p-2 text-zinc-500 transition hover:bg-zinc-100 hover:text-zinc-950 dark:hover:bg-zinc-800 dark:hover:text-white">
                        <span class="sr-only">Close</span>
                        &#10005;
                    </button>
                @endif
            </div>

            <div class="max-h-[75vh] overflow-y-auto px-6 py-5">
                {{ $slot }}
            </div>

            @isset($footer)
                <div class="flex flex-wrap justify-end gap-3 border-t border-zinc-200 px-6 py-4 dark:border-zinc-700">
                    {{ $footer }}
                </div>
            @endisset
        </div>
    </div>
@endif
